# Smart C-Lab Repository Deployment Guide

This guide shows how to deploy Smart C-Lab by cloning from a Git repository.

## Prerequisites

- Windows 10/11
- Administrator access
- Internet connection

## Quick Deployment from Repository

### Step 1: Push to Repository

First, push your code to a Git repository (GitHub, GitLab, etc.):

```bash
# Initialize git repository (if not already done)
git init

# Add all files
git add .

# Commit changes
git commit -m "Initial commit - Smart C-Lab"

# Add remote repository (replace with your repo URL)
git remote add origin https://github.com/YOUR_USERNAME/smart-c-lab.git

# Push to repository
git push -u origin main
```

### Step 2: Deploy on Target Machine

1. **Download the deployment script**:
   - Download `deploy/clone-and-deploy.bat` to the target machine
   - Or clone the repository and run the script from there

2. **Run the deployment script**:
   - Right-click `clone-and-deploy.bat`
   - Select "Run as administrator"
   - The script will automatically:
     - Install Git, PHP, Composer, and NSSM
     - Clone your repository
     - Install the application
     - Create the Windows service
     - Start the application

3. **Access the application**:
   - Open browser and go to `http://localhost:8000`
   - Login with: `admin@smartclab.com` / `password`

## Repository Structure

Make sure your repository includes:

```
smart-c-lab/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── artisan
├── composer.json
├── composer.lock
├── .env.example
└── deploy/
    ├── clone-and-deploy.bat
    ├── update-from-repo.bat
    └── README-REPO.md
```

## Updating the Application

### From Development Machine

1. Make your changes
2. Commit and push:
   ```bash
   git add .
   git commit -m "Update description"
   git push origin main
   ```

### On Target Machine

1. **Right-click** `deploy/update-from-repo.bat`
2. **Select "Run as administrator"**
3. The script will:
   - Stop the service
   - Pull latest changes
   - Update dependencies
   - Run migrations
   - Restart the service

## Manual Update Commands

If you prefer manual updates:

```cmd
# Stop service
sc stop SmartCLab

# Navigate to application
cd C:\smart-c-lab

# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start service
sc start SmartCLab
```

## Service Management

### Check Service Status
```cmd
sc query SmartCLab
```

### Start Service
```cmd
sc start SmartCLab
```

### Stop Service
```cmd
sc stop SmartCLab
```

### Restart Service
```cmd
sc stop SmartCLab && sc start SmartCLab
```

### View Logs
```cmd
type C:\smart-c-lab\storage\logs\service.log
```

## Troubleshooting

### Git Not Found
- The script will automatically download and install Git
- Restart the script after Git installation

### Repository Access Issues
- Ensure the repository is public or you have access
- Check your internet connection
- Verify the repository URL

### Service Won't Start
- Check logs: `C:\smart-c-lab\storage\logs\service.log`
- Verify PHP is installed: `php --version`
- Check service status: `sc query SmartCLab`

### Permission Issues
- Run scripts as Administrator
- Check folder permissions on `C:\smart-c-lab`
- Ensure storage folders are writable

## Benefits of Repository Deployment

### Version Control
- Track all changes
- Easy rollback if needed
- Collaborative development

### Easy Updates
- One command to update
- Automatic dependency management
- Database migration handling

### Backup and Recovery
- Code is safely stored in repository
- Easy to redeploy on new machines
- No need to manually copy files

### Development Workflow
- Make changes locally
- Test on development machine
- Push to repository
- Deploy to production

## Security Considerations

### Repository Access
- Use private repositories for sensitive code
- Consider using SSH keys for authentication
- Regularly update dependencies

### Environment Variables
- Never commit `.env` file
- Use `.env.example` as template
- Set production values during deployment

### Database
- Use SQLite for simple deployments
- Consider MySQL/PostgreSQL for production
- Regular database backups

## Advanced Configuration

### Custom Repository URL
Edit `clone-and-deploy.bat` and change:
```cmd
git clone https://github.com/YOUR_USERNAME/smart-c-lab.git C:\smart-c-lab
```

### Different Branch
To deploy from a different branch:
```cmd
git clone -b production https://github.com/YOUR_USERNAME/smart-c-lab.git C:\smart-c-lab
```

### SSH Authentication
For SSH repositories:
```cmd
git clone git@github.com:YOUR_USERNAME/smart-c-lab.git C:\smart-c-lab
```

## Support

For issues:
1. Check the logs first
2. Verify repository access
3. Ensure scripts are run as Administrator
4. Check Windows Event Viewer for service errors
