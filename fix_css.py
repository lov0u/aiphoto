import paramiko
import base64
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

# Upload the CSS that has gen-page styles (from commit 530b7af)
local_css = r'D:\claud code项目\aiphoto-theme\assets\css\page-styles-with-gen.css'

with open(local_css, 'rb') as f:
    content = f.read()

encoded = base64.b64encode(content).decode('ascii')
tmp = '/tmp/_css_rb.b64'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

parts = []
parts.append('> %s' % tmp)
for i in range(0, len(encoded), 40000):
    parts.append('echo -n "%s" >> %s' % (encoded[i:i+40000], tmp))
cmd = ' && '.join(parts)
s, o, e = ssh.exec_command(cmd)
rc = o.channel.recv_exit_status()

if rc == 0:
    s, o, e = ssh.exec_command('base64 -d %s > %s/assets/css/page-styles.css && rm -f %s' % (tmp, remote_theme, tmp))
    rc = o.channel.recv_exit_status()
    if rc == 0:
        s, o, e = ssh.exec_command('md5sum %s/assets/css/page-styles.css' % remote_theme)
        print("[OK] CSS: " + o.read().decode().strip())
        
        # Verify
        s, o, e = ssh.exec_command('grep -c "gen-page" %s/assets/css/page-styles.css' % remote_theme)
        print("gen-page rules: " + o.read().decode().strip())
        s, o, e = ssh.exec_command('grep -c "agnes-" %s/assets/css/page-styles.css' % remote_theme)
        print("agnes- rules: " + o.read().decode().strip())
    else:
        print("[FAIL] CSS: " + e.read().decode())
else:
    print("[FAIL] b64: " + e.read().decode()[:200])

ssh.close()
