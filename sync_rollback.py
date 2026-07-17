import paramiko
import base64
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

files = [
    ('front-page.php', r'D:\claud code项目\aiphoto-theme\front-page.php'),
    ('assets/css/page-styles.css', r'D:\claud code项目\aiphoto-theme\assets\css\page-styles.css'),
    ('assets/js/main.js', r'D:\claud code项目\aiphoto-theme\assets\js\main.js'),
]

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

for rel, local in files:
    if not os.path.exists(local):
        print("SKIP: %s" % local)
        continue
    with open(local, 'rb') as f:
        data = f.read()
    enc = base64.b64encode(data).decode('ascii')
    tmp = '/tmp/_rb_%s.b64' % rel.replace('/', '_')
    parts = []
    parts.append('> %s' % tmp)
    for i in range(0, len(enc), 40000):
        parts.append('echo -n "%s" >> %s' % (enc[i:i+40000], tmp))
    cmd = ' && '.join(parts)
    s, o, e = ssh.exec_command(cmd)
    rc = o.channel.recv_exit_status()
    if rc != 0:
        print("ERR b64 %s: %s" % (rel, e.read().decode()[:200]))
        continue
    s, o, e = ssh.exec_command('base64 -d %s > %s/%s && rm -f %s' % (tmp, remote_theme, rel, tmp))
    rc = o.channel.recv_exit_status()
    if rc == 0:
        s, o, e = ssh.exec_command('md5sum %s/%s' % (remote_theme, rel))
        print("[OK] %s: %s" % (rel, o.read().decode().strip()))
    else:
        print("[FAIL] %s: %s" % (rel, e.read().decode()))

# Verify
print("\n=== Verify ===")
for rel, local in files:
    if not os.path.exists(local):
        continue
    with open(local, 'r', encoding='utf-8') as f:
        lc = f.read()
    stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page" %s' % local.replace('D:\\claud code项目\\aiphoto-theme\\', '%s/' % remote_theme).replace('\\', '/'))
    gc = stdout.read().decode().strip()
    ac = lc.count('gen-page')
    print("%s: local gen-page=%d, server gen-page=%d" % (rel, ac, gc))

ssh.close()
