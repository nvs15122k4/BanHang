#!/bin/bash
cd /home/nvs1512/BanHang
php artisan serve --host=127.0.0.1 --port=8082 &>/tmp/serve3.log &
SERVER_PID=$!
sleep 3

echo "=== Testing public pages ==="
for url in "/" "/products" "/login" "/register" "/products/1"; do
    code=$(curl -s -o /tmp/page_out.html -w '%{http_code}' "http://127.0.0.1:8082${url}" 2>/dev/null)
    # Check for PHP errors in output
    if grep -q "ErrorException\|ParseError\|Undefined\|Call to\|syntax error" /tmp/page_out.html 2>/dev/null; then
        error=$(grep -o "ErrorException.*\|ParseError.*\|Undefined.*\|Call to.*" /tmp/page_out.html | head -1)
        echo "  ERROR  ${url}: ${code} - ${error}"
    else
        echo "  OK     ${url}: ${code}"
    fi
done

kill $SERVER_PID 2>/dev/null
