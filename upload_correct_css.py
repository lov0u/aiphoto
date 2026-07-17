import paramiko
import base64

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Upload the CORRECT page-styles.css from git (contains BOTH old gen-page AND new agnes styles)
local_path = 'page-styles.css'

with open(local_path, 'rb') as f:
    content = f.read()

encoded = base64.b64encode(content).decode('ascii')
tmp_file = '/tmp/_page_styles.b64'

# Write in chunks
chunk_size = 40000
cmds = []
cmds.append('> %s' % tmp_file)
for i in range(0, len(encoded), chunk_size):
    piece = encoded[i:i+chunk_size]
    cmds.append('echo -n "%s" >> %s' % (piece, tmp_file))

full_cmd = ' && '.join(cmds)
stdin, stdout, stderr = ssh.exec_command(full_cmd)
exit_code = stdout.channel.recv_exit_status()

if exit_code != 0:
    err = stderr.read().decode()
    print("ERROR writing b64: %s" % err[:500])
else:
    stdin, stdout, stderr = ssh.exec_command('base64 -d %s > %s/assets/css/page-styles.css && rm -f %s' % (tmp_file, remote_theme, tmp_file))
    exit_code = stdout.channel.recv_exit_status()
    if exit_code == 0:
        stdin, stdout, stderr = ssh.exec_command('md5sum %s/assets/css/page-styles.css' % remote_theme)
        print("Server: " + stdout.read().decode().strip())
        stdin, stdout, stderr = ssh.exec_command('wc -c < %s/assets/css/page-styles.css' % remote_theme)
        print("Size: " + stdout.read().decode().strip() + " bytes")
        
        # Verify gen-page styles exist
        stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page\\|gen-layout\\|gen-panel" %s/assets/css/page-styles.css' % remote_theme)
        print("gen-page rule count: " + stdout.read().decode().strip())
        print("\nCSS restored with BOTH old and new styles!")
    else:
        print("ERROR: " + stderr.read().decode())

ssh.close()
