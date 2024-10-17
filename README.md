# Switches.mx

## The MX switches Database

Licensed under: [Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)](https://creativecommons.org/licenses/by-nc-sa/4.0/)

If you have any questions, please feel free to contact me at any of the following:

Discord: BOWLER#2802 [Discord Server ](https://discord.gg/pZxjvza)

Reddit: [PM me /u/switchesmx](https://www.reddit.com/message/compose/?to=switchesmx)

## Project structure

The website is built using [Statamic](https://statamic.com/) - the Laravel PHP CMS. It can be run locally using [Laravel Homestead](https://laravel.com/docs/8.x/homestead). The beauty of Statamic is that it's all self-contained with no external database; the content is all stored in yaml and markdown files in the `/content` directory.

## Setting up Switches.mx with Laravel Homestead (Windows)

This tutorial guides you through setting up the Switches.mx project within a Laravel Homestead environment on your Windows machine using Vagrant and VirtualBox.

**Prerequisites:**

* **Install VirtualBox:**
  * Download a version of VirtualBox for Windows hosts from [virtualbox.org](https://www.virtualbox.org/wiki/Downloads).
    > [!IMPORTANT]
    > Check the [supported VirtualBox versions for your Vagrant version](https://developer.hashicorp.com/vagrant/docs/providers/virtualbox) and download a compatible version.
  * Run the installer and follow the on-screen instructions.

* **Install Vagrant:**
  * Download the latest version of Vagrant for Windows from [vagrantup.com](https://www.vagrantup.com/downloads.html).
  * Run the installer and follow the on-screen instructions.

* **Basic terminal knowledge:** You'll be using PowerShell.
* **Git:** For cloning repositories. Download and install from [git-scm.com](https://git-scm.com/downloads).

**1. Install Homestead:**

* Open PowerShell.
* Navigate to the directory where you want to create the Homestead folder (e.g., `$HOME\Projects`):

    ```powershell
    cd $HOME\Projects
    ```

* Clone the Homestead repository and navigate into it:

    ```powershell
    git clone https://github.com/laravel/homestead.git Homestead
    ```

* **Check out the latest stable release:**
  * `git tag --sort=v:refname` (This lists all tags; look for the latest version number)
  * Assuming the latest stable release is `v15.x.x`, do:

    ```powershell
    cd Homestead
    git checkout v12.x.x
    ```

* Initialize Homestead: `.\init.ps1`

**2. Generate an SSH key in the default directory:**

* **Homestead needs an SSH key to securely connect to and manage the virtual machine, even though it's hosted locally.**
* Open a new PowerShell window.
* Run the following command:

    ```powershell
    ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
    ```

    Note: You can use any email address or placeholder here. It's mainly for identification.
* Press Enter to accept the default save location (`$HOME\.ssh\id_rsa`).
* Optionally set a passphrase (and confirm it if you do).

**3. Configure Homestead:**

* Open `Homestead.yaml` in your Homestead directory (`$HOME\Projects\Homestead`) with a text editor.
* **Set up a folder mapping:**

    ```yaml
    folders:
      - map: C:\Code\switches.mx  # Path to your local project folder
        to: /home/vagrant/code/switches.mx 
    ```

* **Configure a site:**

    ```yaml
    sites:
      - map: switches.mx.test
        to: /home/vagrant/code/switches.mx/public
    ```

* **Set the provider:**

    ```yaml
    provider: virtualbox
    ```

* **Add the private key path (default location):**

    ```yaml
    keys:
        - ~/.ssh/id_rsa 
    ```

**4. Update your hosts file:**

* Open `C:\Windows\System32\drivers\etc\hosts` as administrator with a text editor.
* **Check the `ip` address in your `Homestead.yaml` file. It's usually `192.168.56.56`.**
* Add the following line, using the IP address from your `Homestead.yaml`:

    ```text
    192.168.56.56  switches.mx.test
    ```

**5. Clone the project:**

* Open PowerShell.
* Navigate to your projects directory (e.g., `C:\Code`)

    ```powershell
    cd C:\Code
    ```

* Clone the project from GitHub:

    ```powershell
    git clone <github-repository-url> switches.mx
    ```

    Replace `<github-repository-url>` with the actual URL of your GitHub repository (this can be the main repository or your own fork).

**6. Create and set up your `.env` file:**

* Open PowerShell.
* Navigate to your project directory:

    ```powershell
    cd C:\Code\switches.mx
    ```

* Copy the `.env.example` file to create `.env`:

    ```powershell
    cp .env.example .env
    ```

* Open the `.env` file with a text editor and set the necessary environment variables (database credentials, etc.).

**7. Start the Vagrant box:**

* Open PowerShell and navigate to your Homestead directory: `cd $HOME\Projects\Homestead`
* Start the virtual machine: `vagrant up`

**8. SSH into the Vagrant box:**

* In PowerShell, run: `vagrant ssh`

**9. Install Composer dependencies:**

* Navigate to your project directory in the Vagrant box: `cd /home/vagrant/code/switches.mx`
* Ensure the necessary directories exist and have the correct permissions:

    ```bash
    mkdir -p storage/framework/cache
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    sudo chown -R vagrant:vagrant storage 
    ```

* Run this command to update and install the required PHP packages:

    ```bash
    composer update
    ```

**10. Access your switches.mx site:**

* Open your web browser and visit `switches.mx.test`.
