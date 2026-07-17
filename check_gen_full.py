import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Get the full body HTML of /generate page
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | sed -n "/<body/,/<\\/body>/p"')
content = stdout.read().decode()

# Count key elements
checks = {
    'gen-page': content.count('gen-page'),
    'gen-layout': content.count('gen-layout'),
    'gen-panel': content.count('gen-panel'),
    'gen-form': content.count('gen-form'),
    'generatorPrompt': content.count('generatorPrompt'),
    'generatorForm': content.count('generatorForm'),
    'gen-result': content.count('gen-result'),
    'gen-recent-grid': content.count('gen-recent-grid'),
}

print("=== /generate page element counts ===")
for k, v in checks.items():
    print("  %s: %d" % (k, v))

# Show the main structure
print("\n=== Main structure ===")
for line in content.split('\n'):
    stripped = line.strip()
    if stripped.startswith('<section') or stripped.startswith('<div class=') or stripped.startswith('</section') or stripped.startswith('</div'):
        if 'class=' in stripped or stripped.startswith('</section'):
            # Show first 100 chars
            cls_start = stripped.find('class=')
            if cls_start >= 0:
                display = stripped[:cls_start+50] + '...'
            else:
                display = stripped[:100]
            print(display)

ssh.close()
