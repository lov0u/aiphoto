import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Find the exact line range of the generate section
# Look for the closing of elseif and next elseif/endif
stdin, stdout, stderr = ssh.exec_command("grep -n 'elseif.*generate\\|elseif.*gallery\\|elseif.*video\\|elseif.*chat\\|endif;' /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php | head -20")
print("=== Section boundaries ===")
print(stdout.read().decode())

# Get the generate section end
stdin, stdout, stderr = ssh.exec_command("sed -n '347,600p' /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php | grep -n 'elseif\\|endif\\|</section' | head -10")
print("=== Generate section end markers ===")
print(stdout.read().decode())

# Get lines around where generate section ends
stdin, stdout, stderr = ssh.exec_command("sed -n '500,560p' /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php")
print("=== Lines 500-560 (around generate section end) ===")
for i, line in enumerate(stdout.read().decode().split('\n')):
    print("%3d: %s" % (500+i, line[:120]))

ssh.close()
