# Docker Setup for Clinic Appointment System

## Prerequisites

- Docker installed and running
- Docker Compose installed

## Quick Start

### 1. Start the Containers

```bash
docker-compose up -d
```

This will start three services:

- **MySQL** (port 3306)
- **phpMyAdmin** (port 8080)
- **Apache + PHP** (port 80)

### 2. Access Your Application

- **Main Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080
- **phpMyAdmin Credentials**:
  - Username: `clinic_user`
  - Password: `clinic_password`

### 3. Database Setup

The MySQL database will be automatically initialized with your `database.sql` file on first run.

## Common Commands

### View Logs

```bash
docker-compose logs -f web
docker-compose logs -f mysql
docker-compose logs -f phpmyadmin
```

### Stop Containers

```bash
docker-compose down
```

### Stop and Remove Data

```bash
docker-compose down -v
```

### Rebuild Images

```bash
docker-compose build --no-cache
docker-compose up -d
```

### Access MySQL from Host

```bash
mysql -h 127.0.0.1 -P 3306 -u clinic_user -p clinic_password clinic_db
```

## File Structure

- `docker-compose.yml` - Orchestrates all containers
- `Dockerfile` - Builds PHP/Apache image
- `apache-config.conf` - Apache VirtualHost configuration
- `.dockerignore` - Files to exclude from Docker build

## Database Credentials (Default)

- **Host**: mysql (or 127.0.0.1 from host)
- **User**: clinic_user
- **Password**: clinic_password
- **Database**: clinic_db
- **Root Password**: root_password

## Troubleshooting

### Port Already in Use

Change ports in `docker-compose.yml`:

```yaml
ports:
  - "3307:3306" # MySQL on different port
  - "8081:80" # phpMyAdmin on different port
  - "8000:80" # Apache on different port
```

### Database Connection Issues

Ensure MySQL is running:

```bash
docker-compose ps
```

Check if service is healthy:

```bash
docker-compose logs mysql
```

### PHP Extensions Missing

The Dockerfile includes mysqli, pdo_mysql, and GD extensions. To add more:

1. Edit the `Dockerfile`
2. Rebuild: `docker-compose build --no-cache`

## Development Tips

- Your project files are mounted as a volume, so changes are reflected immediately
- Database files persist in the `mysql_data` volume
- Enable Apache `mod_rewrite` for clean URLs (already enabled)
