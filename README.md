# 🏥 Healthy Life Clinic - Web Application  

Welcome to the **Healthy Life Clinic** web application. This project is designed as part of the BIT608 assessment 2 

---

## 🚀 How to Run the Project  

### **1️⃣ Open the Login Page**
- Go to: [Login Page](http://localhost/clinic/login.php)  
- Users must log in using **their email** (not name or surname).  

### **2️⃣ Access PHPMyAdmin (Database Management)**
- Open: [PHPMyAdmin](http://localhost/phpmyadmin)  
- Database Name: **clinic**  
- MySQL Username: **root**  
- MySQL Password: **root**  

---

## 🔑 Test Credentials (Login)  

| User Type | Email | Password |
|-----------|----------------------|------------------|
| **Admin** 👨‍💼 | admin@example.com | adminPassword123 |
| **Admin** 👨‍💼 | mainadmin@example.com | mainAdmin123 |
| **Admin** 👨‍💼 | ppaladmin@example.com | principalAdmin456 |
| **Patient 1** 👤 | s.stallone@example.com | johnPassword123 |
| **Patient 2** 👤 | b.willis@example.com | janePassword123 |
| **Patient 3** 👤 | a.schwarzenegger@example.com | terminator123 |
| **Patient 4** 👤 | j.vandamme@example.com | kickboxer123 |
| **Patient 5** 👤 | s.seagal@example.com | aikido123 |

📌 **Note:**  
- **Passwords are encrypted in the database** (`password_hash()` in PHP).  
- If you need to reset a password, you must update it in the database and hash it again.  

---

## 🔄 **Login Redirection - What Happens After Login?**  

| User Role | Redirects To | Features Available |
|-----------|------------|---------------------|
| **Admin** 👨‍💼 | `admin_dashboard.php` | Manage Patients, Manage Providers, List & Edit Appointments |
| **Patient** 👤 | `index.php` | View, Create, and Manage Their Own Appointments |

📌 **Admins have full system access**, while **patients can only see "their own" data**.  

---

## 🗂 **Project Structure & Page Connections**  

txt
clinic/
│-- config.php        # Database connection & session management
│-- login.php         # User login page
│-- logout.php        # Ends session & redirects to login
│-- index.php         # Homepage (Patients' Dashboard)
│-- admin_dashboard.php # Admin Panel for managing data
│-- manage_patients.php # Admin: Manage Patients
│-- manage_providers.php # Admin: Manage Providers
│-- list_appointments.php # List of all appointments
│-- make_appointment.php # Create a new appointment
│-- appointment_details.php # View single appointment details
│-- edit_appointment.php # Modify an existing appointment
│-- delete_appointment.php # Delete an appointment
│-- check_availability.php # AJAX-powered time slot validation
│-- style/style2.css  # Custom styles for all pages



---

## 🔒 Security Features Implemented  

✅ **Password Hashing** → Passwords are stored securely using `password_hash()`.  
✅ **Session Handling** → `session_start()` ensures secure user access control.  
✅ **Role-Based Access** → Patients & Admins have different permissions.  
✅ **AJAX for Real-Time Availability Check** → Prevents duplicate appointments.  

---

## 🛠 How to Import the Database?  

1️⃣ **Open PHPMyAdmin:** [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
2️⃣ **Select the `clinic` database** (or create it if it doesn’t exist).  
3️⃣ **Click on "Import"** and upload the `clinic.sql` file.  
4️⃣ **Click "Go"** to execute the SQL script and import the data.  

---

## 🔄 How Pages Interact  

- **`login.php`** → Redirects users to `admin_dashboard.php` (Admin) or `index.php` (Patient).  
- **`admin_dashboard.php`** → Manages patients, providers, and appointments.  
- **`make_appointment.php`** → Uses **AJAX** to check available time slots.  
- **`check_availability.php`** → Prevents booking the same time for the same provider.  
- **`edit_appointment.php` / `delete_appointment.php`** → Allows appointment modifications.  
- **`logout.php`** → Ends the session and redirects back to `login.php`.  

---

## 🎯 Final Notes  

- This project was built using **PHP, MySQL, JavaScript, AJAX, and CSS**.  
- If you face login issues, check the **database users and hashed passwords**.  
- For any doubts, review `config.php` for database settings.  
- **Note:** The file `update_patient_passwords.php` was used temporarily to rehash patient passwords but has been **removed from the final version** for security reasons.
