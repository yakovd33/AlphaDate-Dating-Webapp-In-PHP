<?php require('includes/global.php'); ?>

<?php include 'includes/header.php'; ?>

<?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'] - 1;
    } else {
        $page = 0;
    }

    $users_per_page = 10;
    $pages_to_show_on_pagination = 5;

    $users_stmt = $GLOBALS['link']->query("SELECT * FROM `users` LIMIT " . $page * $users_per_page . ', ' . $users_per_page);
    $total_users_count = $GLOBALS['link']->query("SELECT * FROM `users`")->rowCount();
?>

<table class="table" id="users-table" dir="rtl">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">שם מלא</th>
			<th scope="col">אימייל</th>
            <th scope="col">מין</th>
            <th scope="col">פעולות</th>
		</tr>
	</thead>
	<tbody>
        <?php while ($user = $users_stmt->fetch()) : ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['fullname']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['gender'] == 'male' ? 'זכר' : 'נקבה'; ?></td>
                <td>
                    <a href="delete_user.php?user_id=<?php echo $user['id']; ?>"><button type="button" class="btn cur-p btn-danger"><i class="fas fa-trash-alt"></i></button></a>
                    <a href="edit_user.php?user_id=<?php echo $user['id']; ?>"><button type="button" class="btn cur-p btn-warning"><i class="fas fa-pencil-alt"></i></button></a>
                    <a href="user_blocks.php?user_id=<?php echo $user['id']; ?>"><button type="button" class="btn cur-p btn-info">חסימות</button></a>
                </td>
            </tr>
        <?php endwhile; ?>
	</tbody>
</table>

<div class="btn-group mr-2" role="group" aria-label="First group">
    <?php
        $pagination_start = ($page - $pages_to_show_on_pagination <= 0) ? 1 : $page - $pages_to_show_on_pagination;
        $pagination_end = (($page + $pages_to_show_on_pagination) * $users_per_page) > $total_users_count ? ceil($total_users_count / $users_per_page) + 1 : $page + $pages_to_show_on_pagination;
    ?>
    <?php for ($i = $pagination_start; $i < $pagination_end; $i++) : ?>
        <a href="users.php?page=<?php echo $i; ?>"><button type="button" class="btn btn-success"><?php echo $i; ?></button></a>
    <?php endfor; ?>
</div>

<script>
    $(document).ready( function () {
        $('#users-table').DataTable({
            "paging": false
        });
    });
</script>

<?php include 'includes/footer.php'; ?>