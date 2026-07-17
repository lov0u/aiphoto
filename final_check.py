import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check home page
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/ 2>&1 | grep -c "lp-hero"')
home_has_hero = stdout.read().decode().strip()
print("Home page has lp-hero: %s" % home_has_hero)

# Check generate page
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | grep -c "gen-page"')
gen_has_page = stdout.read().decode().strip()
print("Generate page has gen-page: %s" % gen_has_page)

# Check CSS loads correctly
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | grep "page-styles.css"')
print("\nCSS link:")
print(stdout.read().decode().strip())

# Quick sanity: check if CSS has both old and new styles
stdin, stdout, stderr = ssh.exec_command('grep -c "gen-" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("\ngen- style rules: %s" % stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('grep -c "agnes-" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
print("agnes- style rules: %s" % stdout.read().decode().strip())

ssh.close()
