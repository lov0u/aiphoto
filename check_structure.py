import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check the full structure of front-page.php on server
stdin, stdout, stderr = ssh.exec_command('grep -n "^<?php\\|^<section\\|^<div class=\\|^</section\\|^</div>\\|elseif\\|endif" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php')
print("=== Structure of front-page.php ===")
print(stdout.read().decode())

# Check the end of the file
stdin, stdout, stderr = ssh.exec_command('tail -30 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php')
print("\n=== Last 30 lines ===")
print(stdout.read().decode())

# Check the generate section specifically
stdin, stdout, stderr = ssh.exec_command("grep -n \"elseif.*generate\\|elseif.*gallery\\|elseif.*video\\|elseif.*chat\" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php")
print("\n=== Page sections ===")
print(stdout.read().decode())

ssh.close()
