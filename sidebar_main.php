<!-- main sidebar -->

<div class="sidebar">
    <div class="menu">
        <?php echo ($highlight == "Login") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="index.php">Login</a></h4>
        </div>
        <?php echo ($highlight == "Create student account") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="create_own_account.php">Create student account</a></h4>
        </div>
        <?php echo ($highlight == "Contact") ? "<div class='highlight'>" : "<div class='menu-item'>"; ?>
            <h4><a href="contact.php">Contact</a></h4>
        </div>
    </div> 
</div>
