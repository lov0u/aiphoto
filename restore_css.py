import paramiko
import base64
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Upload the ORIGINAL page-styles.css from git to restore normal display
local_path = 'page-styles-original.css'

with open(local_path, 'rb') as f:
    content = f.read()

encoded = base64.b64encode(content).decode('ascii')
tmp_file = '/tmp/_restore_css.b64'

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
    # Decode and write
    stdin, stdout, stderr = ssh.exec_command('base64 -d %s > %s/assets/css/page-styles.css && rm -f %s' % (tmp_file, remote_theme, tmp_file))
    exit_code = stdout.channel.recv_exit_status()
    if exit_code == 0:
        # Verify
        stdin, stdout, stderr = ssh.exec_command('md5sum %s/assets/css/page-styles.css' % remote_theme)
        print("Server CSS: " + stdout.read().decode().strip())
        stdin, stdout, stderr = ssh.exec_command('wc -c < %s/assets/css/page-styles.css' % remote_theme)
        print("Size: " + stdout.read().decode().strip() + " bytes")
        print("CSS RESTORED - page should look normal now")
    else:
        print("ERROR: " + stderr.read().decode())

ssh.close()
