#!/bin/bash

if [[ $EUID -ne 0 ]]; then
    echo "Error: This script must be run as root (sudo)"
    exit 1
fi

MY_IP="212.156.221.208"

echo "=== UFW Yapılandırması Başlıyor ==="

ufw --force reset

ufw default deny incoming
ufw default allow outgoing

ufw allow from $MY_IP to any port 22 proto tcp comment "SSH - My IP"

ufw allow 80/tcp comment "HTTP"
ufw allow 443/tcp comment "HTTPS"

ufw --force enable

echo ""
echo "=== UFW Durumu ==="
ufw status verbose

echo ""
echo "Yapılandırma tamamlandı."
echo "  SSH: sadece $MY_IP icin acik"
echo "  HTTP (80): herkese acik"
echo "  HTTPS (443): herkese acik"
echo "  Diger tum portlar: kapali"
