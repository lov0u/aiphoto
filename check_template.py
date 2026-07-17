import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# 1. Check what page ID 2716 is using
stdin, stdout, stderr = ssh.exec_command("wp post get 2716 --field=post_type --format=csv 2>/dev/null || mysql -u root -p'zhang_LEI758' -e \"SELECT ID, post_type, post_name FROM wp_posts WHERE ID=2716\" 2>/dev/null || echo 'cannot query db'")
print("=== Page info ===")
print(stdout.read().decode())

# 2. Check the database for the page
stdin, stdout, stderr = ssh.exec_command("mysql -u root -e \"SHOW DATABASES;\" 2>/dev/null | head -20")
print("=== MySQL databases ===")
print(stdout.read().decode())

# 3. Find the DB credentials
stdin, stdout, stderr = ssh.exec_command("grep 'DB_NAME' /www/wwwroot/aiphoto/wp-config.php")
print("=== DB Config ===")
print(stdout.read().decode())

# 4. Query the page
stdin, stdout, stderr = ssh.exec_command("mysql -u root -e \"USE aiphoto; SELECT ID, post_type, post_name, post_status FROM wp_posts WHERE post_name='generate' OR ID=2716;\" 2>/dev/null")
print("=== Generate page in DB ===")
print(stdout.read().decode())

# 5. Check which template file WordPress is actually using
stdin, stdout, stderr = ssh.exec_command("mysql -u root -e \"USE aiphoto; SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id=2716 AND meta_key='_wp_page_template';\" 2>/dev/null")
print("=== Page template ===")
print(stdout.read().decode())

# 6. Check the actual page-generate.php content on server
print("\n=== page-generate.php content (first 50 lines) ===")
stdin, stdout, stderr = ssh.exec_command('head -50 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/page-generate.php')
print(stdout.read().decode())

# 7. Check if there's a page-generate.php in the root of theme or somewhere else
print("\n=== All page-*.php files ===")
stdin, stdout, stderr = ssh.exec_command('find /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme -name "page-*.php" -exec basename {} \\;')
print(stdout.read().decode())

# 8. Check the page template hierarchy - what does WordPress think the template is?
print("\n=== Front-page.php content (template part) ===")
stdin, stdout, stderr = ssh.exec_command("grep -n 'generate\\|page-generate\\|template' /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php | head -20")
print(stdout.read().decode())

ssh.close()
