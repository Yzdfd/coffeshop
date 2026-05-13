        </div><!-- end page content -->
    </div><!-- end main content -->
</div><!-- end wrapper -->
<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

<?php
// Popup from flashdata (optional)
$popupType = session()->getFlashdata('popup_type');
$popupTitle = session()->getFlashdata('popup_title');
$popupMsg = session()->getFlashdata('popup_message');
if (!empty($popupType) && !empty($popupMsg)) {
    echo '<script>window.CAFEF_POPUP = ' . json_encode([
        'type' => $popupType,
        'title' => $popupTitle ?? 'Info',
        'message' => $popupMsg,
    ]) . ';</script>';
} else {
    echo '<script>window.CAFEF_POPUP = null;</script>';
}
?>
<!-- Global JS -->
<script src="<?= base_url('js/script.js') ?>"></script>
</body>
</html>
