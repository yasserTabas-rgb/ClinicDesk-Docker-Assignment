# Technical Notes & Assignment Reflections

## 1. Biggest Docker Challenge & Solution
- **The Challenge:** Establishing a stable database connection from inside the container to the local MySQL server (XAMPP) and enabling URL rewriting for the MVC routing system (`.htaccess`).
- **The Solution:** 
  - Configured the database host connection to use `host.docker.internal` instead of `localhost` to bridge networking correctly.
  - Enabled Apache's `mod_rewrite` module by adding `RUN a2enmod rewrite` directly into the `Dockerfile`.

## 2. Key Git/GitHub Lesson Learned
- The importance of adhering to professional semantic commit message conventions (e.g., `feat:`, `docker:`, `docs:`) to maintain a clean history, track project progress logically, and make collaboration seamless.