import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Get the FULL generate page body
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1')
content = stdout.read().decode()

# Count occurrences
old_classes = {
    'gen-page': content.count('gen-page'),
    'gen-layout': content.count('gen-layout'),
    'gen-panel': content.count('gen-panel'),
    'agnes-app': content.count('agnes-app'),
    'agnes-sidebar': content.count('agnes-sidebar'),
    'agnes-main': content.count('agnes-main'),
    'agnes-welcome': content.count('agnes-welcome'),
    'lp-hero': content.count('lp-hero'),
}

print("=== Class usage in /generate page ===")
for cls, count in old_classes.items():
    status = "NEW" if "agnes" in cls else "OLD"
    print("  [%s] %s: %d" % (status, cls, count))

# Show body content structure
print("\n=== Body structure ===")
in_body = False
for i, line in enumerate(content.split('\n')):
    if '<body' in line:
        in_body = True
    if in_body:
        stripped = line.strip()
        if stripped.startswith('<section') or stripped.startswith('<main') or stripped.startswith('<div class=') or stripped.startswith('</section') or stripped.startswith('</main') or 'gen-page' in stripped or 'agnes' in stripped.lower():
            print("%3d: %s" % (i+1, stripped[:100]))

ssh.close()
