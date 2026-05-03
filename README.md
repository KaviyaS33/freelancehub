# 💼 FreelanceHub

## 📌 Project Overview

FreelanceHub is a web-based platform that connects clients and freelancers.
Clients can post job opportunities, and freelancers can browse and apply for those jobs.

---

## 🎥 Project Demo Video

You can view the working demo of the project here:
https://drive.google.com/file/d/1_di1YJOUql4F0m7XQLnQknK_PBeZVTrh/view?usp=drive_link

---

## 🚀 Features

### 👨‍💼 Admin

* Admin login
* View users
* Monitor jobs and applications

### 👤 Client

* Register and login
* Post job listings
* Manage posted jobs

### 👨‍💻 Freelancer

* Register and login
* Browse available jobs
* Apply for jobs with proposals

---

## 🛠️ Technologies Used

* PHP (Backend)
* MySQL (Database)
* HTML, CSS (Frontend)
* JavaScript (Basic interactivity)

---

## 📂 Project Structure

```
freelancehub/
│
├── admin/
├── client/
├── freelancer/
├── css/
├── db.php
├── index.php
├── freelancehub.sql
└── README.md
```

---

## ⚙️ Setup Instructions

### 1️⃣ Clone the Repository

```
git clone https://github.com/KaviyaS33/freelancehub.git
```

---

### 2️⃣ Import Database

* Open MySQL Workbench
* Create a database named:

  ```
  freelancehub
  ```
* Import the file:

  ```
  freelancehub.sql
  ```

---

### 3️⃣ Configure Database Connection

Open `db.php` and update:

```php
$conn = new mysqli("127.0.0.1", "root", "your_password", "freelancehub");
```

⚠️ Replace `"your_password"` with your MySQL root password.

---

### 4️⃣ Run the Project

Open terminal in project folder and run:

```
php -S localhost:8000
```

---

### 5️⃣ Open in Browser

```
http://localhost:8000
```

---

## 🔐 Admin Login

```
Username: admin
Password: admin123
```

---

## 🔗 Project Link

GitHub Repository:
https://github.com/KaviyaS33/freelancehub

---

## 📷 Screenshots

(Add screenshots of your project here for better presentation)

---

## 📌 Notes

* Make sure MySQL server is running before starting the project
* Importing the SQL file is required to avoid database errors

---

## 👩‍💻 Author

Kaviya
