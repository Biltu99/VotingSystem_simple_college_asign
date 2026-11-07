
```markdown
# 🗳️ Simple Online Voting System (PHP & MySQL)

A secure, beginner-friendly Online Voting System designed for a college election. This project ensures the core democratic principle of "One Person, One Vote" using PHP, MySQL, and PDO.

## 📌 Project Overview
This system allows students (voters) to register, log in, and cast their vote exactly once. It includes a separate **Officer Panel** to manage election candidates and party names, and a public **Results page** that displays the winner using visual progress bars.

### ✨ Key Features
- **Voter Registration & Login:** Secure authentication using password hashing.
- **One-Vote Rule:** Enforced by both PHP logic and a MySQL UNIQUE constraint.
- **Officer Role:** A special dashboard to add or delete candidates/parties.
- **Live Results:** Publicly viewable results with percentage bars and winner highlights.
- **Responsive Design:** Clean Black, Red, and White UI using pure CSS.
- **Security:** Protection against SQL Injection (PDO prepared statements) and XSS (`htmlspecialchars`).

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3 (Vanilla), Basic JavaScript
- **Backend:** Core PHP
- **Database:** MySQL
- **Server:** XAMPP

---

## 📂 Project Structure
```text
VotingSystem_simple/
│
├── index.php               # Home page
├── register.php            # Voter registration
├── login.php               # Voter login
├── dashboard.php           # Voter dashboard (shows candidates)
├── vote.php                # Vote processing logic
├── results.php             # Public results page
├── logout.php              # Session destroy
├── officer_login.php       # Officer login page
├── officer_dashboard.php   # Officer panel to manage parties
├── setup_officer.php       # Temporary script to create the first officer
├── db.php                  # PDO database connection
├── style.css               # Main styling file
└── database.sql            # Database creation script
```

---

## 🚀 Installation & Setup Guide

### Step 1: Start XAMPP
1. Open the XAMPP Control Panel.
2. Start the **Apache** and **MySQL** services.

### Step 2: Setup the Project Folder
1. Copy the `VotingSystem_simple` folder.
2. Paste it inside `C:\xampp_new\htdocs\`.

### Step 3: Import the Database
1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Click on the **Import** tab.
3. Choose the `database.sql` file from the project folder.
4. Click **Go** (or Import). 
   *(This will create the database `online_voting_simple` and the required tables).*

### Step 4: Run the App
1. Open your browser.
2. Go to: `http://localhost/VotingSystem_simple/index.php`

---

## 🔑 Default Login Credentials

### For Voter:
Voters must register first. Click on **Register** on the home page and create an account.

### For Election Officer:
The system comes with a default officer account, or you can create one using the setup script.
1. Visit `http://localhost/VotingSystem_simple/setup_officer.php` (Run this once).
2. **Username:** `officer1`
3. **Password:** `officer123`
4. **Important:** Delete the `setup_officer.php` file after running it for security.

---

## 🗄️ Database Schema

The system uses three simple tables:

**1. `users`**
Stores both voters and officers.
- `id` (INT, Primary Key)
- `name` (VARCHAR)
- `username` (VARCHAR, Unique)
- `password` (VARCHAR - Bcrypt Hash)
- `role` (ENUM: 'voter', 'officer')

**2. `candidates`**
Stores the parties and candidates.
- `id` (INT, Primary Key)
- `name` (VARCHAR)
- `party` (VARCHAR)

**3. `votes`**
Stores the voting records.
- `id` (INT, Primary Key)
- `user_id` (INT, Foreign Key)
- `candidate_id` (INT, Foreign Key)
- **`UNIQUE(user_id)`** — *This is the most critical line. It ensures a user can only appear once in this table.*

---

## 🛡️ How Double Voting is Prevented

This project strictly enforces the "One Person, One Vote" rule through **3 layers of security**:

1. **Dashboard UI (`dashboard.php`):** Hides the vote buttons if the user has already voted.
2. **Server Logic (`vote.php`):** Checks the database before inserting. If a vote exists, it redirects the user.
3. **Database Constraint (`votes` table):** The `UNIQUE(user_id)` constraint physically blocks a second vote from being saved, even if someone tries to hack the URL.

---

## 🧪 Testing Checklist (For Viva/Practical Exam)

- [ ] Register a new voter.
- [ ] Login as the voter.
- [ ] Cast a vote for Candidate A.
- [ ] Verify the "You have already voted" message appears.
- [ ] Try to manually visit `vote.php?id=2` in the URL -> Verify you are redirected.
- [ ] Login as Officer (`officer1` / `officer123`).
- [ ] Add a new party/candidate.
- [ ] Verify the new candidate appears on the Voter's dashboard.
- [ ] Open the Results page and check the progress bars and winner highlight.

---

## 👨‍💻 Author
**Biltu**
BCA Student | College Academic Project

```
