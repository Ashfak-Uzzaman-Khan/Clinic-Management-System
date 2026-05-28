Title:  Clinic Management System

## Project Description

The **Clinic Management System** is a comprehensive **web-based application** developed using **PHP** and **MySQL**.
It aims to simplify and automate the day-to-day operations of clinics by managing **patients, doctors, appointments, and medical services** efficiently.

The system enables patients to book appointments, doctors to manage schedules and prescriptions, and administrators to oversee all clinical activities.
It provides a **secure, user-friendly, and organized platform** for effective communication and service delivery between patients, doctors, and the clinic.


# Table of Contents

1. [Features](#-features)
2. [Type of Users](#-type-of-users)
3. [Technologies Used](#-technologies-used)
4. [How to Install and Run the Project](#-how-to-install-and-run-the-project)
5. [How to Use the Project](#-how-to-use-the-project)
6. [Developers](#-developers)
7. [Project Information](#-project-information)

---

## Features

### Type of Users

1. **Patient**
2. **Admin**
3. **Doctor**

---

### Common Features (Available to All Users)

* **Authentication:** Secure login and logout system
* **User Registration:** Patients and Doctors can register (Doctors require Admin approval before practice)
* **Account Management:**

  * Change or reset password
  * View, edit, or delete profile information
* **Dashboard:**

  * Personalized dashboard view after login

---

### Features of Doctor

* Create and manage available appointment slots
* View and update patient medical reports
* Generate prescriptions for patients
* Communicate with patients after appointments

---

### Features of Patient

* Manage personal information (name, age, location, medical history, etc.)
* Submit test or operation requests
* Book appointments with available doctors

---

### Features of Admin

* Review and approve test operation requests submitted by patients
* Assign doctors to patients and manage appointment scheduling
* Approve doctor registration requests
* Monitor and manage all user activities

---

## Technologies Used

| Component    | Technology            |
| ------------ | --------------------- |
| **Frontend** | HTML, CSS, JavaScript |
| **Backend**  | PHP                   |
| **Database** | MySQL                 |
| **Server**   | XAMPP                 |

---

## How to Install and Run the Project

1. Install **XAMPP** and start **Apache** and **MySQL**.
2. Copy the project folder into:

   ```
   C:\xampp\htdocs\Clinic_Management_System\
   ```
3. Open **phpMyAdmin** and create a database named:

   ```
   hdb
   ```
4. Import the SQL file:

   ```
   hdb.sql
   ```
5. Open your browser and navigate to:

   ```
   http://localhost/Clinic_Management_System/index.php
   ```

---

## How to Use the Project

1. Open your browser and go to:

   ```
   http://localhost/Clinic_Management_System/index.php
   ```
2. Navigate to the **Login** page using the navigation bar.
3. **Patients** and **Doctors** can register — but **Doctors must wait for Admin approval**.
4. After login:

   * **Patients:** Manage profiles, request tests, and book appointments.
   * **Doctors:** Manage appointments and update patient prescriptions.
   * **Admin:** Oversee, approve, and manage all clinic-related activities.

---

## Developers

| Name                              | Email                                                             | Institution                                         |
| --------------------------------- | ----------------------------------------------------------------- | --------------------------------------------------- |
| **Md. Ashfak Uzzaman Khan Limon** | [22-49976-3@student.aiub.edu](mailto:22-49976-3@student.aiub.edu) | American International University-Bangladesh (AIUB) |


---

## Project Information

* **Course:** Web Technologies
* **Instructor:** WAHIDUL ALAM RIYAD ([wahid.riyad@aiub.edu](mailto:wahid.riyad@aiub.edu))
* **Semester:** Summer 2025

---

Would you like me to make it **slightly shorter and more formal (for printed report submission)** version too?
That one would sound like an academic report abstract instead of a README.
