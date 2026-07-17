import paramiko
import base64
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

# Read the Agnes template
template_path = r'D:\claud code项目\aiphoto-theme\_agnes_generate_section.php'
with open(template_path, 'r', encoding='utf-8') as f:
    new_section = f.read()

# Read current front-page.php
local_fp = r'D:\claud code项目\aiphoto-theme\front-page.php'
with open(local_fp, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find generate section boundaries
gen_start = None
gen_end = None
for i, line in enumerate(lines):
    stripped = line.strip()
    if "<?php elseif ( 'generate' === $page ) :" in stripped and gen_start is None:
        gen_start = i
    if gen_start is not None and "<?php elseif ( 'gallery' === $page ) :" in stripped:
        gen_end = i
        break

print("Replace lines %d-%d (0-indexed)" % (gen_start, gen_end))

# Replace
new_lines = lines[:gen_start] + [new_section.rstrip('\n') + '\n'] + lines[gen_end:]

# Write back
with open(local_fp, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print("Local file updated.")

# Upload via base64
with open(local_fp, 'rb') as f:
    content = f.read()

encoded = base64.b64encode(content).decode('ascii')
tmp_file = '/tmp/_fp_upload.b64'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

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
    stdin, stdout, stderr = ssh.exec_command('base64 -d %s > %s/front-page.php && rm -f %s' % (tmp_file, remote_theme, tmp_file))
    exit_code = stdout.channel.recv_exit_status()
    if exit_code == 0:
        stdin, stdout, stderr = ssh.exec_command('md5sum %s/front-page.php' % remote_theme)
        print("Server: " + stdout.read().decode().strip())
        stdin, stdout, stderr = ssh.exec_command('wc -l < %s/front-page.php' % remote_theme)
        print("Lines: " + stdout.read().decode().strip())
        
        # Verify
        stdin, stdout, stderr = ssh.exec_command('grep -c "agnes-app\\|agnes-sidebar\\|agnes-welcome" %s/front-page.php' % remote_theme)
        print("Agnes elements found: " + stdout.read().decode().strip())
        
        stdin, stdout, stderr = ssh.exec_command('grep -c "gen-page\\|gen-layout" %s/front-page.php' % remote_theme)
        print("Old gen-page elements: " + stdout.read().decode().strip())
        
        print("\nDONE!")
    else:
        print("ERROR: " + stderr.read().decode())

ssh.close()
