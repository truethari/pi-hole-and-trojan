# Pi-hole + Trojan Docker Setup

This repository contains a Docker Compose setup for running Pi-hole (DNS ad-blocker) and Trojan (proxy server) on the same VPS.

## Prerequisites

- Docker and Docker Compose installed
- A domain name pointing to your VPS
- SSL certificates for your domain

## Docker Installation

If you don't have Docker installed, follow these instructions for your operating system:

### Ubuntu/Debian
```bash
# Update package index
sudo apt update

# Install dependencies
sudo apt install apt-transport-https ca-certificates curl gnupg lsb-release

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Update package index and install Docker
sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Add user to docker group (optional, to run docker without sudo)
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker compose version
```

### CentOS/RHEL/Fedora
```bash
# Install dependencies
sudo yum install -y yum-utils

# Add Docker repository
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

# Install Docker
sudo yum install docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Start and enable Docker
sudo systemctl start docker
sudo systemctl enable docker

# Add user to docker group (optional)
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker compose version
```

### Alternative: Docker Compose (standalone)
If you need the standalone Docker Compose:
```bash
# Download Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Make it executable
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker-compose --version
```

## Setup Instructions

### 1. Configure Environment Variables

Edit the `.env` file and update the following:

```bash
# Replace with a secure password for Pi-hole web interface
PIHOLE_PASSWORD=your_secure_pihole_password_here

# Replace with your VPS public IP address
SERVER_IP=your_vps_public_ip_here

```

### 2. Configure Trojan

Edit `trojan/config.json` and update:

- Replace `your_trojan_password_here` with a secure password
- The configuration assumes SSL certificates are in `trojan/certs/`

### 3. SSL Certificates

Place your SSL certificates in the `trojan/certs/` directory:

```bash
trojan/certs/
├── fullchain.pem
└── privkey.pem
```

You can obtain free SSL certificates using Let's Encrypt:

```bash
# Install certbot
sudo apt install certbot

# Get certificates
sudo certbot certonly --standalone -d your-domain.com

# Copy certificates
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem trojan/certs/
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem trojan/certs/
sudo chown $USER:$USER trojan/certs/*
```

### 4. Start Services

```bash
docker-compose up -d
```

### 5. Verify Setup

- Pi-hole web interface: `http://your-vps-ip`
- Trojan runs on port 443

## Services

### Pi-hole
- **Web Interface**: Port 80
- **DNS**: Port 53 (TCP/UDP)
- **Purpose**: DNS-based ad blocking
- **Data**: Stored in `etc-pihole/` and `etc-dnsmasq.d/`

### Trojan
- **Port**: 443
- **Purpose**: Secure proxy server
- **Configuration**: `trojan/config.json`
- **Certificates**: `trojan/certs/`
- **DNS**: Uses local Pi-hole for ad-blocking on proxied traffic

**Benefits**: 
- All traffic routed through Trojan will automatically benefit from Pi-hole's ad-blocking without requiring external DNS lookups

## Client Configuration

### For Trojan Client

Use the following settings in your Trojan client:

- **Server**: your-domain.com
- **Port**: 443
- **Password**: (the password you set in config.json)
- **SNI**: your-domain.com

### For Pi-hole DNS

Configure your devices to use your VPS IP as DNS server:

- **Primary DNS**: your-vps-ip
- **Secondary DNS**: 1.1.1.1 (or any fallback)


## Maintenance

### Update containers:
```bash
docker-compose pull
docker-compose up -d
```

### View logs:
```bash
docker-compose logs -f pihole
docker-compose logs -f trojan
```

### Stop services:
```bash
docker-compose down
```

## Security Notes

- Change default passwords immediately
- Keep SSL certificates updated
- Regularly update Docker images
- Monitor logs for suspicious activity
- Consider firewall rules to restrict access