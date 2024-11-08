<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $attachments = $_FILES['attachments'];

    // 이메일 형식 확인
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("잘못된 이메일 형식입니다.");
    }

    $to = "userid@naver.com";
    $subject = "문의 폼에서 새 문의사항이 도착했습니다.";
    $boundary = md5(time());
    $headers = "From: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // 이메일 내용 작성
    $email_message = "--$boundary\r\n";
    $email_message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_message .= "Content-Transfer-Encoding: 7bit\r\n";
    $email_message .= "\r\n";
    $email_message .= "이름: $name\n";
    $email_message .= "휴대폰번호: $phone\n";
    $email_message .= "이메일: $email\n";
    $email_message .= "문의내용: $message\n";

    // 첨부 파일 추가
    if (!empty($attachments['name'][0])) {
        for ($i = 0; $i < count($attachments['name']) && $i < 3; $i++) {
            if ($attachments['error'][$i] == 0) {
                $file_path = $attachments['tmp_name'][$i];
                $file_name = $attachments['name'][$i];
                $file_content = chunk_split(base64_encode(file_get_contents($file_path)));

                $email_message .= "--$boundary\r\n";
                $email_message .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
                $email_message .= "Content-Transfer-Encoding: base64\r\n";
                $email_message .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
                $email_message .= "\r\n";
                $email_message .= $file_content . "\r\n";
            }
        }
    }

    $email_message .= "--$boundary--";

    // 메일 보내기
    if (mail($to, $subject, $email_message, $headers)) {
        echo "<meta charset=\"UTF-8\">";
		echo "<script>
            alert('문의가 성공적으로 전송되었습니다!');
            locatioh.href='form.php';
          </script>";
    } else {
		echo "<meta charset=\"UTF-8\">";
        echo "<script>
            alert('문의 전송에 실패했습니다. 다시 시도해 주세요.');
            history.back();
          </script>";
    }
} else {
    header("Location: form.php");
    exit();
}
?>
