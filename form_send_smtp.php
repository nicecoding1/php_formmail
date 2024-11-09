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
