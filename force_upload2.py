import paramiko
import os
import base64

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

files = [
    ('assets/css/page-styles.css', r'D:\claud code项目\aiphoto-theme\assets\css\page-styles.css'),
    ('assets/js/main.js', r'D:\claud code项目\aiphoto-theme\assets\js\main.js'),
]

for rel_path, local_path in files:
    remote_path = '%s/%s' % (remote_theme, rel_path)
    
    if not os.path.exists(local_path):
        print("SKIP: %s not found" % local_path)
        continue
    
    with open(local_path, 'rb') as f:
        content = f.read()
    
    encoded = base64.b64encode(content).decode('ascii')
    tmp_file = '/tmp/_upload_%s.b64' % rel_path.replace('/', '_')
    
    # Write base64 to temp file on server
    # Split into multiple echo commands to avoid shell limit
    chunk = 40000
    cmds = []
    cmds.append('> %s' % tmp_file)
    for i in range(0, len(encoded), chunk):
        piece = encoded[i:i+chunk]
        cmds.append('echo -n "%s" >> %s' % (piece, tmp_file))
    
    full_cmd = ' && '.join(cmds)
    stdin, stdout, stderr = ssh.exec_command(full_cmd)
    exit_code = stdout.channel.recv_exit_status()
    
    if exit_code != 0:
        err = stderr.read().decode()
        print("ERROR writing b64 for %s: %s" % (rel_path, err[:200]))
        # Try alternative: use python3
        print("  Trying python3 approach...")
        stdin, stdout, stderr = ssh.exec_command(
            'python3 -c "import base64; f=open(\'%s\',\'wb\'); f.write(base64.b64decode(\'%s\')); f.close(); print(\'OK\')" 2>&1' 
            % (tmp_file, encoded[:30000])
        )
        print("  Result:", stdout.read().decode().strip())
        continue
    
    # Decode and write to target
    stdin, stdout, stderr = ssh.exec_command('base64 -d %s > %s && rm -f %s' % (tmp_file, remote_path, tmp_file))
    exit_code = stdout.channel.recv_exit_status()
    
    if exit_code != 0:
        print("ERROR decoding %s: %s" % (rel_path, stderr.read().decode()))
    else:
        stdin, stdout, stderr = ssh.exec_command('wc -c < %s' % remote_path)
        remote_size = stdout.read().decode().strip()
        local_size = os.path.getsize(local_path)
        match = "OK" if str(local_size) == remote_size else "MISMATCH"
        print("[%s] %s (local:%s remote:%s)" % (match, rel_path, local_size, remote_size))

# Verify
print("\n=== MD5 verification ===")
for rel_path, local_path in files + [('page-generate.php', r'D:\claud code项目\aiphoto-theme\page-generate.php')]:
    remote_path = '%s/%s' % (remote_theme, rel_path)
    stdin, stdout, stderr = ssh.exec_command('md5sum "%s"' % remote_path)
    print(rel_path + ": " + stdout.read().decode().strip())

ssh.close()
