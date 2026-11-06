</main>
<footer class="site-footer"><div class="container footer-inner">
<div><strong><?= e(
    APP_NAME,
) ?></strong><p>Laptop và thiết bị máy tính cho học tập, làm việc và giải trí.</p></div>
<div class="student-info">MSSV: <?= e(STUDENT_ID) ?> · Họ tên: <?= e(
     STUDENT_NAME,
 ) ?></div>
</div><div class="copyright">© <?= date("Y") ?> <?= e(
     APP_NAME,
 ) ?>. Website đồ án học phần.</div></footer>
<script src="<?= e(url("js/main.js")) ?>"></script></body></html>
