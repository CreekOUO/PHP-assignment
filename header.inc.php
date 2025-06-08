<header style=" display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 30px;
    border-bottom: 1px solid #000000;">
    <a href="materialsPage.php" style="font-size: 28px; font-weight: bold; text-decoration: none; color: black;">
        BrushShare
    </a>
    <form action="materialsPage.php" method="get" class="search-bar" style="display: flex; align-items: center;">
        <input type="text" name="q" placeholder="Search..." style="padding: 6px; font-size: 16px;">
        <button type="submit" style="background: none; border: none; cursor: pointer; margin-left: 5px;">
            <img src="images/search.png" alt="Search" style="width: 24px; height: 24px;">
        </button>
    </form>
    <div style="display: flex; align-items: center; gap: 10px;">
        <?php if (isset($_SESSION['user'])): ?>
            <a href="profile.php">
                <img src="images/profile.png" alt="profile" style="width: 24px; height: 24px; cursor: pointer;">
            </a>
            <div><?= htmlspecialchars($_SESSION['user']['username']) ?></div>
            <a href="logout.php" style="text-decoration: none; padding: 6px 12px; border: 1px solid #333; border-radius: 5px; color: #333;">Logout</a>
        <?php else: ?>
            <img src="images/profile.png" alt="profile" style="width: 24px; height: 24px; cursor: pointer;">
            <a href="login.php" style="text-decoration: none; padding: 6px 12px; border: 1px solid #333; border-radius: 5px; color: #333;">Log in</a>
            <a href="signup.php" style="text-decoration: none; padding: 6px 12px; background-color: #333; color: white; border-radius: 5px;">Sign Up</a>
        <?php endif; ?>
    </div>

</header>