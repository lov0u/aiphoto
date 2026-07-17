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

# Read the local front-page.php
local_path = r'D:\claud code项目\aiphoto-theme\front-page.php'
with open(local_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

total_lines = len(lines)
print("Total lines in front-page.php: %d" % total_lines)

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

print("Generate section: line %d to line %d (exclusive)" % (gen_start + 1, gen_end + 1))

# Read the Agnes-style generate template
template_path = r'D:\claud code项目\aiphoto-theme\agnes_generate_template.php'
with open(template_path, 'r', encoding='utf-8') as f:
    new_section = f.read()

print("New section size: %d chars" % len(new_section))

# Replace the section
new_lines = lines[:gen_start] + [new_section + '\n'] + lines[gen_end:]

# Write back
with open(local_path, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print("Local file updated.")

# Upload via base64
with open(local_path, 'rb') as f:
    content = f.read()

encoded = base64.b64encode(content).decode('ascii')
tmp_file = '/tmp/_frontpage.b64'

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
    stdin, stdout, stderr = ssh.exec_command('base64 -d %s > %s/front-page.php.new && mv %s/front-page.php.new %s/front-page.php && rm -f %s' % (tmp_file, remote_theme, remote_theme, remote_theme, tmp_file))
    exit_code = stdout.channel.recv_exit_status()
    if exit_code == 0:
        print("Uploaded successfully!")
        # Verify
        stdin, stdout, stderr = ssh.exec_command('wc -c < %s/front-page.php' % remote_theme)
        remote_size = stdout.read().decode().strip()
        local_size = os.path.getsize(local_path)
        print("Local: %s bytes, Remote: %s bytes" % (local_size, remote_size))
    else:
        print("ERROR uploading: %s" % stderr.read().decode())

ssh.close()
