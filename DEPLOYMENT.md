# Deploying e-CERTIFY on a barangay computer (offline)

After this setup, the barangay only has to **turn on the computer and open the browser**. Nobody starts XAMPP, runs `php artisan serve` or `npm run dev`.

**How it works**
- Apache and MySQL are installed as **Windows services**, so they start by themselves when the PC turns on.
- Apache serves the app directly, so `php artisan serve` is not needed.
- `npm run dev` is not needed at all. All CSS, icons, fonts and charts are already included in `public/vendor`, so the system works with **no internet**.
- The barangay PC does not need Composer, Node or Git. You prepare everything on your own PC and bring it over on a USB drive.

You will do **Part A on your PC** (with internet) and **Part B on the barangay PC**.

---

## Part A: Prepare the package (your PC)

1. Make sure your latest work is saved to GitHub (`git add .`, `git commit`, `git push`).
2. In your project folder, double-click **`deploy\make-release.bat`**.
3. It creates a folder named **`e-certify-release`** next to your project. Copy that folder to a USB drive.

It leaves out your `.env`, your uploaded logos and signature, and your logs, so no passwords or test data travel with it.

---

## Part B: Set up the barangay PC (one time)

### 1. Install XAMPP
Install XAMPP to `C:\xampp` (take the installer on the USB drive). It must include **PHP 8.2 or newer**. To check, open Command Prompt and run:
```
C:\xampp\php\php.exe -v
```

### 2. Copy the app
Copy the contents of `e-certify-release` into **`C:\xampp\htdocs\e-certify`**. You should end up with `C:\xampp\htdocs\e-certify\artisan`.

### 3. Make Apache and MySQL start by themselves
1. Open the **XAMPP Control Panel** as administrator (right-click, Run as administrator).
2. Next to **Apache**, click the red **X** under "Service", then **Yes**. It turns into a green check.
3. Do the same for **MySQL**.
4. Click **Start** on both.

From now on they start with Windows. You never need to open the Control Panel again, except to fix problems.

### 4. Create the database and a private user
1. Open **http://localhost/phpmyadmin** in the browser.
2. Click the **SQL** tab, paste this (change `YOUR_PASSWORD`), and click **Go**:
```sql
CREATE DATABASE e_certify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ecertify'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON e_certify.* TO 'ecertify'@'localhost';
```
Write that password down. You need it in step 6.

### 5. Protect MySQL (important)
XAMPP's MySQL starts with an **empty root password**. Fix that:
1. In the XAMPP Control Panel click **Shell** and run (change `NEWROOTPASS`):
   ```
   mysqladmin -u root password NEWROOTPASS
   ```
2. Open `C:\xampp\phpMyAdmin\config.inc.php` in Notepad and set the line
   `$cfg['Servers'][$i]['password'] = 'NEWROOTPASS';`
3. Open `C:\xampp\mysql\bin\my.ini`, find the `[mysqld]` section and add the line `bind-address=127.0.0.1`. This stops other computers from reaching the database.
4. In the Control Panel, **Stop** then **Start** MySQL.

### 6. Give the app its web address
1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf` in Notepad and paste the contents of **`deploy\ecertify-vhost.conf`** at the **top** of the file. Save.
2. Open **Notepad as administrator**, open `C:\Windows\System32\drivers\etc\hosts`, add this line at the bottom, and save:
   ```
   127.0.0.1   ecertify.local
   ```
3. In the Control Panel, restart **Apache** (Stop, then Start).

### 7. Run the setup script
1. Right-click **`C:\xampp\htdocs\e-certify\deploy\setup-barangay-pc.bat`** and choose **Run as administrator**.
2. The first time, it opens `.env` in Notepad. Replace `CHANGE_THIS_PASSWORD` with the database password from step 4, save, close Notepad.
3. Run the script **again**. It creates the tables and links the uploads folder.

### 8. Create the first Admin account
In Command Prompt:
```
cd C:\xampp\htdocs\e-certify
C:\xampp\php\php.exe artisan tinker
```
Paste this (use your own details), then press Enter. Type `exit` when done.
```php
\App\Models\User::create(['name' => 'Your Name', 'email' => 'you@example.com', 'password' => 'a-strong-password', 'role' => 'Admin']);
```
Add the other staff later from the **Users** page.

### 9. Open it
Go to **http://ecertify.local**, sign in, then:
1. **Barangay**: enter this barangay's name, municipality and province.
2. **Certificates, Settings & Assets**: captain's name, header/footer, logos, QR code and signature.

Right-click the desktop, choose New, then Shortcut, and enter `http://ecertify.local` to make a desktop icon.

### 10. The real test
**Restart the computer. Don't open XAMPP.** Open the browser and go to `http://ecertify.local`. If the login page appears, the setup is done.

---

## Using it from other computers (optional)

If several staff need it at once, keep the system on one computer and open it from the others over the office network or router. No internet is needed.

1. On the server PC, run `ipconfig` and note the **IPv4 address**, such as `192.168.1.10`.
2. Give that PC a fixed address (in the router's DHCP reservation settings, or in the Windows network settings) so it never changes.
3. Allow it through the firewall: open **Windows Defender Firewall, Advanced settings, Inbound Rules, New Rule, Port, TCP 80, Allow**, for **Private** networks only.
4. On the other computers, open `http://192.168.1.10` (use your own address).

---

## Backups (please set this up)

All the barangay's certificate records live in this one database, so back it up.

1. Open **`deploy\backup-database.bat`** in Notepad and set the database password and the `DEST` folder (best: a USB or external drive).
2. Double-click it once to test. A `.sql` file should appear in that folder.
3. Schedule it daily at 5:30 PM. Open Command Prompt as administrator and run (change the path if needed):
   ```
   schtasks /create /tn "e-CERTIFY Backup" /tr "C:\xampp\htdocs\e-certify\deploy\backup-database.bat" /sc daily /st 17:30
   ```
It keeps 60 days of backups and also copies the uploaded logos and signature.

**To restore:** in phpMyAdmin, select the `e_certify` database, choose **Import**, and pick the `.sql` file.

---

## Updating later

1. On your PC: run `deploy\make-release.bat` again and copy the new folder to a USB drive.
2. On the barangay PC: **run the backup first**.
3. Copy the new files over `C:\xampp\htdocs\e-certify`, choosing "Replace". Your `.env` and your uploaded images are not in the package, so they stay safe.
4. Run **`deploy\setup-barangay-pc.bat`** as administrator. It applies any database changes.

---

## Troubleshooting

| Problem | What to do |
|---|---|
| Apache won't start | Something else is using port 80, often Skype or the Windows "World Wide Web Publishing Service". Stop that service, or close the program, then start Apache again. |
| "Server Error 500" | Open `storage\logs\laravel.log` and read the last lines. Usually the `.env` database password is wrong or MySQL is not running. |
| Page looks plain, no icons | Press **Ctrl+F5**. If it persists, check that the `public\vendor` folder exists. |
| `ecertify.local` not found | The line is missing in the `hosts` file (step 6.2), or Notepad wasn't run as administrator when saving it. |
| Shows the XAMPP welcome page | The vhost block must be at the **top** of `httpd-vhosts.conf`, and Apache must be restarted. |
| Logos or signature don't appear | Run `setup-barangay-pc.bat` as administrator again (it recreates the uploads link). |
| Wrong date on certificates | Fix the Windows date, time and time zone (Philippines, UTC+8). |
| Changed `.env` but nothing changed | Run `C:\xampp\php\php.exe artisan config:clear` in the project folder. |

**Good to know:** public registration and "Forgot password" are switched off in this version. Only an Admin can create accounts and reset passwords, from the **Users** page. A power cut is the main risk to the database, so a small UPS is worth having.
