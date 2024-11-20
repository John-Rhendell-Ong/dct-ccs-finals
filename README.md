# Student Management System

This project is a PHP-based Student Management System, featuring login authentication, CRUD operations for students and subjects, and grade assignment functionality. This README file will guide you through setting up, understanding, and developing the project, following the required specifications.

## Table of Contents

- [Project Structure](#project-structure)
- [Database Setup](#database-setup)
- [Features Overview](#features-overview)
- [Technical Requirements](#technical-requirements)
- [Detailed Instructions](#detailed-instructions)
- [Grading Criteria](#grading-criteria)
- [Developer Guidelines](#developer-guidelines)

---

## Project Structure

The project directory includes the following key folders and files:

- **`admin/`**: Contains management functionality for students and subjects.
  - **`student/`**: Subdirectory with student-related functionality.
  - **`partials/`**: Contains reusable layout files (header, sidebar, footer).
- **`functions.php`**: Contains reusable PHP functions.
- **`index.php`**: The login page for the system.
 
Thank you for sharing the full directory structure! Here’s a detailed breakdown for the README file that mirrors your project's file organization.

---

```
root/
├── index.php                           # Login page for authentication entry point.
├── functions.php                       # Contains reusable functions for database access, authentication, and validation.

├── admin/                              # Admin section for managing students and subjects.
│   ├── partials/                       # Directory for shared UI components and styling.
│   │   ├── custom-dashboard.css        # Custom styles for the dashboard.
│   │   ├── footer.php                  # Footer template for consistent UI across pages.
│   │   ├── header.php                  # Header template, included in all admin pages for consistency.
│   │   └── side-bar.php                # Sidebar navigation for easy access to admin pages.
│
│   ├── student/                        # Directory for student-related functionality.
│   │   ├── assign-grade.php            # Assigns or updates grades for a student's subject.
│   │   ├── attach-subject.php          # Page to assign subjects to a selected student.
│   │   ├── delete.php                  # Deletes a student record from the system.
│   │   ├── dettach-subject.php         # Detaches a subject from a selected student.
│   │   ├── edit.php                    # Edits existing student information.
│   │   └── register.php                # Registers a new student with form validation.
│
│   ├── subject/                        # Directory for subject-related functionality.
│   │   ├── add.php                     # Adds a new subject to the system.
│   │   ├── delete.php                  # Deletes an existing subject.
│   │   └── edit.php                    # Edits information for an existing subject.
│
│   ├── dashboard.php                   # Main dashboard page after login, accessible to authenticated users.
│   └── logout.php                      # Ends user session and redirects to the login page.
```


---

## Database Setup

1. **Import the Database**:
   - An SQL file with the database schema is included in the project repository.
   - Import this file into your MySQL database. **Do not modify the table structure**, as this ensures consistent evaluation across all students.

2. **Database Details**:
   - The database contains tables for students, subjects, and relationships between them. 
   - `students_subjects` table is used to track the subjects assigned to students, including their grades.

---

## Features Overview

### Dashboard
- **Subject Count**: Shows how many subjects are available in the database.
- **Student Count** : Shows how many students are registered in the database.
- **Number of Students Who Failed:** Count the students whose average grade across all assigned subjects is below the passing threshold.
- **Number of Passed Students:**  Count the students whose average grade across all assigned subjects is above the passing threshold.

### Authentication
- **Login System** with MD5 password hashing for secure access.
- **Session Guard** applied to all pages to ensure restricted access only to authenticated users.
- **Logout** functionality to safely exit the session.

### Students Management
- **Register Student**: Add a new student to the database.
- **Edit Student**: Update student information.
- **Delete Student**: Remove a student from the database.
- **Attach Subject**: Assign subjects to students.
- **Detach Subject**: Remove a subject from a student’s record.
- **Assign Grade**: Record a grade for a student in a specific subject.

### Subject Management
- **Add Subject**: Create a new subject.
- **Edit Subject**: Modify subject details.
- **Delete Subject**: Remove a subject from the database.

### General Requirements
- **Password Hashing**: MD5 hash must be used for password storage.
- **Server-Side Validation Only**: All input validations should be done on the server; disable client-side validation (e.g., `required`, `type="email"`).
- **PHP Templating**: Use templates for consistent layouts.
- **Sidebar Active State**: Display the active page in the sidebar for user navigation.
- **Input Preservation on Error**: Ensure form inputs retain previous values on error.

---

## Technical Requirements

- **PHP 7.x or 8.x**
- **MySQL Database**
- **Git** for version control
- **Laragon, WAMP or XAMPP** contains both PHP and MySQL

---

## Detailed Instructions

### Step 1: Clone the Repository

Clone the project repository to your local development environment: (inside your laragon www folder)

```bash
git clone https://github.com/senseihatakekakashi/dct-ccs-finals.git
cd dct-ccs-finals
code .
```

### Step 2: Database Configuration

1. Import the provided SQL file to set up your database structure.
2. Configure your database connection in the `functions.php` file.

### Step 3: Set Up PHP Sessions and Authentication

- Implement session handling in `functions.php` for secure access.
- Use `md5()` for password hashing during login and registration.

### Step 4: Develop Core Features

Each feature should follow the specifications below. For each file, refer to `functions.php` for reusable logic.

#### **Authentication**
- Implement login using the `index.php` file.
- Securely store passwords using MD5 hashing and perform authentication in `functions.php`.
- Set up a session guard function (`guard()`) to restrict access to authenticated users.

#### **CRUD Operations**

- **Students**:
  - Register: `register.php` should add new students with error handling.
  - Edit: `edit-student.php` should populate fields with existing data.
  - Delete: Confirmation required before deleting records.
  - Attach/Detach Subject: Use `attach-subject.php` and `dettach-subject.php` to link subjects.
  - Assign Grade: Use `assign-grade.php` to enter grades for subjects.

- **Subjects**:
  - Add, Edit, Delete: Accessible under the **Subjects** section in the sidebar.

### Step 5: Set Up UI and Templating

- **HTML Templates**: Use `partials/` for consistent headers, footers, and sidebars.
- **Sidebar Active State**: Dynamically highlight the active page.

### Step 6: Git and Version Control

- Use Git for version control with branching and regular commits. Each feature should have descriptive commit messages.
- Follow the branching strategy as per the industry standards the we discussed in details. See [Developer Guidelines](#developer-guidelines)

---

## Grading Criteria

| Feature                    | Description                                      | Score |
|----------------------------|--------------------------------------------------|-------|
| Login                      | MD5 hashing, authentication guard                |     10|
| Dashboard                  | Display relevant data, secure access             |     10|
| Logout                     | Clears session                                   |      5|
| Subjects                   | Add, Edit, Delete                                |     15|
| Students                   | Register, Edit, Delete, Attach, Detach, Grade    |     20|
| Functions                  | Consistent use of reusable functions             |     10|
| PHP Templating             | Header, sidebar, footer                          |      5|
| Password Hashing           | MD5 password hashing                             |      5|
| Authentication Guard       | Session-based access control                     |      5|
| Server-Side Validation     | No client-side validation                        |      5|
| UI Consistency             | Matches provided HTML templates                  |      5|
| Git Branching and Commits  | Proper branching, meaningful commits             |      5|
| Input Preservation on Error| Retain input values if validation fails          |      5|
| **Total**                  |                                                  |    100|

---

## Developer Guidelines

- **Git Branching**: Create feature branches for each functionality (e.g., `feature/add-subject`, `feature/assign-grade`).
- **Commit Messages**: Write meaningful commit messages that explain what each change does.
- **Coding Standards**: Follow PHP best practices and ensure code readability.
- **Testing**: Test each feature before committing. Ensure error handling works, and all server-side validations are effective.

---

## Final Remarks

This final exam provides practical experience in PHP, MySQL, and software development best practices. Be sure to follow the instructions carefully, refer to `functions.php` for reusable functions, and implement each feature according to specifications. Use Git effectively to document your work, and maintain the directory structure as provided.

Happy coding, and good luck with your finals!

Got it! Here’s a good one for that last section:

---

> **Friendly Reminder:** "Cheating might seem like a shortcut, but remember, copying code isn’t like copying your classmates answer in the exam. Coding has a clever way of exposing the true author—trust me, I have a sharingan, I see *everything*. 😉 So save yourself the hassle, and let’s keep it honest. Your future self will thank you!"