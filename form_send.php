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

    // 문자 보내기
    // 문자서비스: 제이문자 https://jmunja.com 회원가입 시 API 사용 체크
    $sms_subject = "문의사항 도착"; // 제목
    $sms_content = ""; // 내용
    $sms_content .= "이름: $name\n";
    $sms_content .= "휴대폰번호: $phone\n";
    $sms_content .= "이메일: $email\n";
    $sms_content .= "문의내용: $message\n";
    $sms_admin_hp = "01012345678"; // 수신번호
    $sms_id = ""; // 제이문자 아이디
    $sms_key = ""; // 제이문자 API KEY

    try {
        $cmd = "curl --request POST 'https://jmunja.com/sms/app/api_v2.php' --form 'id={$sms_id}' --form 'pw={$sms_key}' --form 'mode=send' --form 'title={$sms_subject}' --form 'message={$sms_content}' --form 'reqlist={$sms_admin_hp}'";
        exec($cmd, $output, $return_var);
    } catch (Exception $e) {
        echo "<meta charset=\"UTF-8\">";
        echo "문자 발송 중 예외가 발생했습니다: " . $e->getMessage() . "\n";
    }
	
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
