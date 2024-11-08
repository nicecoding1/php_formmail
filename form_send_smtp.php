<?php
// PHPMailer, SMTP, Exception 네임스페이스 포함
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer 관련 파일들 포함
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 폼 데이터 수집
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $attachments = $_FILES['attachments'];

    // SMTP 설정
    $mail = new PHPMailer(true);

    try {
        // 서버 설정
        $mail->isSMTP();  // SMTP 사용
        $mail->CharSet    = "utf-8";
        $mail->Encoding   = "base64";
        $mail->Host = 'smtp.naver.com';  // SMTP 서버 주소 (예: smtp.gmail.com)
        $mail->SMTPAuth = true;  // SMTP 인증 활성화
        $mail->Username = 'user_id';  // SMTP 사용자 이메일
        $mail->Password = 'user_password';  // SMTP 사용자 비밀번호
        $mail->SMTPSecure = 'ssl';  // 암호화 방식 (TLS)
        $mail->Port = 465;  // SMTP 포트 (587은 TLS 포트, 465는 SSL 포트)

        // 발신자 및 수신자 설정
        $mail->setFrom('user_id@naver.com', '폼메일 관리자');
        $mail->addAddress('admin@naver.com', '관리자');  // 수신자 이메일 주소

        // 이메일 내용
        $mail->isHTML(true);  // HTML 형식 사용
        $mail->Subject = '새로운 문의가 도착했습니다';
        $mail->Body    = "이름: $name<br>휴대폰 번호: $phone<br>이메일 주소: $email<br>문의 내용: $message";

        // 파일 첨부 처리
        if (!empty($attachments['name'][0])) {
            for ($i = 0; $i < count($attachments['name']) && $i < 3; $i++) {
                if ($attachments['error'][$i] == 0) {
                    $file_path = $attachments['tmp_name'][$i];
                    $file_name = $attachments['name'][$i];
                    // 첨부파일 추가
                    $mail->addAttachment($file_path, $file_name);
                } else {
                    // 파일 업로드 오류 처리
                    echo "<meta charset=\"UTF-8\">";
                    echo "파일 첨부 오류: {$attachments['error'][$i]}<br>";
                }
            }
        }

        // 이메일 전송
        $mail->send();
        echo "<meta charset=\"UTF-8\">";
            echo "<script>
                alert('문의가 성공적으로 전송되었습니다!');
                location.href='form.php';
              </script>";
        exit;
    } catch (Exception $e) {
        echo "<meta charset=\"UTF-8\">";
        echo "<script>
                alert('문의 전송에 실패했습니다. 다시 시도해 주세요. 오류: {$mail->ErrorInfo}');
                history.back();
              </script>";
    }
} else {
    header("Location: form.php");
    exit();
}
?>
