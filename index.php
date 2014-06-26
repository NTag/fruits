<?php
if(!isset($_GET['search_line'])) {
	header('Location: /');
} else {
	header('Location: /#/search/' . urlencode($_GET['search_line']));
}
exit();