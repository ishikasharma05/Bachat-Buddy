# Bachat-Buddy 💰
A personal budget management system with income/expense tracking,
dynamic graphs, goal progress, and a rule-based AI adviser.

## Features
- Income & expense tracking
- Real-time chart visualisation
- Smart savings tips based on spending ratios
- Goal progress tracking

## Tech Stack
PHP | JavaScript | HTML/CSS | Data Visualisation

## Screenshots
<img width="152" height="251" alt="image" src="https://github.com/user-attachments/assets/d4902a43-3064-4d84-9235-41ca0fa971f1" />  --- sign up page 

<img width="197" height="241" alt="image" src="https://github.com/user-attachments/assets/b3dc0076-3807-4ec5-8d44-d5352f7b622c" /> ----login page 

<img width="480" height="205" alt="image" src="https://github.com/user-attachments/assets/8ed71522-356e-4318-bf70-29f9bd5b163f" /> -----dashboard 

<img width="167" height="235" alt="image" src="https://github.com/user-attachments/assets/301bff11-ec5c-4b57-a810-8b94b492c63f" />  ---- chatbot (rule-based)

<img width="458" height="203" alt="image" src="https://github.com/user-attachments/assets/50cfe0f4-3495-4c54-bec6-19ea68187a68" /> ---- goals page 

<img width="353" height="264" alt="image" src="https://github.com/user-attachments/assets/5fe90687-48e8-4f3e-8d7d-d35788e9903f" /> ----expense entry 

## How To Run
### Prerequisites
Before running this project, make sure you have:
- [XAMPP](https://www.apachefriends.org/) installed (includes PHP + MySQL + Apache)
- A browser (Chrome recommended)

---

### Step 1 — Download the Project

**Option A — Clone with Git:**
```bash
git clone https://github.com/ishikasharma05/Bachat-Buddy.git
```

**Option B — Download ZIP:**
- Click the green `Code` button on this repo
- Click `Download ZIP`
- Extract the folder

### Step 2 — Move to XAMPP

Copy the entire `Bachat-Buddy` folder and paste it inside:
So the path looks like:
---

### Step 3 — Start XAMPP

- Open XAMPP Control Panel
- Start **Apache**
- Start **MySQL**

---

### Step 4 — Create the Database

- Open your browser and go to: `http://localhost/phpmyadmin`
- Click **New** on the left side
- Create a database named: `bachat_buddy`
- Click the `bachat_buddy` database
- Click **Import** tab at the top
- Find and import the SQL file from the `config/` folder in this project

---

### Step 5 — Configure Database Connection

Open the file `config/db.php` and check these details match your XAMPP setup:

```php
$host = "localhost";
$user = "root";
$password = "";        // Leave empty for default XAMPP
$database = "bachat_buddy";
```

---

### Step 6 — Run the Project

Open your browser and go to:
http://localhost/Bachat-Buddy/

## 🙋‍♀️ Built By

**Ishika Sharma**
B.Sc. IT Graduate | Navi Mumbai

[![Portfolio](https://img.shields.io/badge/Portfolio-000?style=flat)](https://ishikasharma05.github.io/ishikasharma.github.io/)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=flat&logo=linkedin)](https://www.linkedin.com/in/ishika-sharma-connect/)
[![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat&logo=github)](https://github.com/ishikasharma05)
