<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>문의 폼</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 10px;
            background-color: #f7f9fc;
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], textarea, input[type="file"] {
            width: 100%; 
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
			margin-right: 10px; /* 우측 여백 10px 유지 */
			box-sizing: border-box;
        }
        input[type="button"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="button"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>문의하기</h2>
    <form action="form_send.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="phone">휴대폰번호</label>
            <input type="text" id="phone" name="phone" required placeholder="숫자만 입력해 주세요">
        </div>
        <div class="form-group">
            <label for="email">이메일주소</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">문의내용</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="attachments">파일 첨부 (최대 3개)</label>
            <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" max="3">
        </div>
        <input type="button" value="전송하기" onclick="formCheck()">
    </form>
</div>

</body>
</html>

<script>
function formCheck() {
	const name = document.getElementById("name").value.trim();
	const phone = document.getElementById("phone").value.trim();
	const email = document.getElementById("email").value.trim();
	const message = document.getElementById("message").value.trim();
	const attachments = document.getElementsByName("attachments[]");

	// 이름 유효성 검사
	if (name === "") {
		alert("이름을 입력해 주세요.");
		return false;
	}

	// 휴대폰 번호 유효성 검사 (숫자만 허용)
	const phoneRegex = /^[0-9]{10,11}$/;
	if (!phoneRegex.test(phone)) {
		alert("휴대폰 번호를 올바르게 입력해 주세요.");
		return false;
	}

	// 이메일 유효성 검사
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (!emailRegex.test(email)) {
		alert("유효한 이메일 주소를 입력해 주세요.");
		return false;
	}

	// 문의내용 유효성 검사
	if (message === "") {
		alert("문의내용을 입력해 주세요.");
		return false;
	}

	// 파일 첨부 개수 및 용량 검사
	let fileCount = 0;
	for (let i = 0; i < attachments.length; i++) {
		if (attachments[i].files.length > 0) {
			fileCount += attachments[i].files.length;
			for (let j = 0; j < attachments[i].files.length; j++) {
				if (attachments[i].files[j].size > 5 * 1024 * 1024) { // 파일 크기 5MB 제한
					alert("각 첨부 파일의 크기는 5MB를 초과할 수 없습니다.");
					return false;
				}
			}
		}
	}
	if (fileCount > 3) {
		alert("최대 3개의 파일만 첨부할 수 있습니다.");
		return false;
	}

	// 모든 검사 통과 시 폼 제출
	if(!confirm("폼을 제출하시겠습니까?")) return false;
	document.querySelector("form").submit();
}
</script>