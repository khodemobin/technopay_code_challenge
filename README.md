## 🧠 TechnoPay Code Challenge
### Overview
At **TechnoPay**, we deKBsign systems that prioritize security, integrity, and scalability. In this challenge, you are tasked with implementing a secure wallet purchase process that ensures safe and consistent transactions.

### 🎯 Objective
Design and implement a wallet-based purchase system that ensures each transaction is explicitly confirmed before the wallet balance is deducted. The confirmation process should allow integrating two-step verification mechanisms, and the system must guarantee consistency, integrity, and safe execution even under concurrent requests.

### 🧾 Scenario
Users have wallet accounts with a balance they can use to make purchases. Your task is to build a system that supports:

#### ✅ Functional Requirements
The system should:


**1- Invoice payment support**
>The system should be able to handle paying an invoice. The purchase has already been created, and now the invoice needs to be paid. It also has an expiration time.

**2- User must not be blocked.**

**3- Wallet must be active.**

**4- Ownership Enforcement**
>Invoice must belogns to this user.

**5- Two-step verification**
>Ensure that the purchase is confirmed before deducting the wallet balance to maintain integrity and prevent inconsistencies.

**6- Insufficient Balance Handling**
>If the wallet balance is insufficient, return an appropriate error and do not process the transaction.

**7-  Daily Spending Limit**
>There is a global daily spending limit for all users combined. Prevent any invoice from being paid if the total value of already-paid invoices on the same day has reached this threshold.

**8-  Accurate Timestamps**
>The system should record the time when the invoice is paid.

**9-  Notifications**
>Notify the user appropriately after a successful or failed transaction.

**10-  Refund on Failure**
>If an error occurs during the payment process, ensure that the amount is refunded to the wallet.

**11- Mock External Services**
>If the system requires integration with external services, use mock services with fixed responses. No real integration is needed.

**12- Ensure the response provides all necessary details**

### ⚠️ Concurrency & Safety
The entire implementation must be safe for concurrent access, preventing race conditions and guaranteeing data consistency.

### 🧱 Architecture & Design Requirements
Use **Laravel v11+**

No external packages

Code must be clean, readable, and maintainable

Follow design patterns and `SOLID` principles

Ensure the system is scalable and easy to extend

Including a README file is required. The README should include the project structure and the design patterns used.

### 🧪 Testing
Provide **unit** and **feature** tests

Include test cases for success, failure, and edge scenarios (e.g., simultaneous purchases)

### Additional Points
You get extra points if you Dockerize the project.

### 🚚 Submission Guidelines
Upload your code to a GitHub repository

Share the link by replying to this email:
📧 fouladgar.dev@gmail.com

### 🤝 Final Notes
If you have any questions, feel free to reach out. We're looking forward to seeing how you ensure safety, clarity, and scalability in your solution.

Thank you for your time and interest in joining **TechnoPay**!
