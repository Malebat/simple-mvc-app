<h1><?= $data['Page']['title']; ?></h1>


<?php if(!isset($_SESSION['User'])): ?>
    <div style="width:140px; margin: 64px auto; text-align:center; line-height:240%;">
        <form method="post">
            <input name="f_login" placeholder="логин" style="width:100px;" maxlength="64" required><br>
            <input name="f_password" placeholder="пассворд" style="width:100px;" maxlength="64" required><br>
            <input value="Ввод" type="submit">
        </form>
    </div>
<?php else: ?>
    <p>Вы уже авторизованы, вроде.</p>
    <p>А можно <a href="/user/logout" title="Выход">выйти</a></p>
<?php endif; ?>

<?= $data['Page']['content']; ?>