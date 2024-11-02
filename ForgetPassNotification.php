 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Đặt lại mật khẩu của bạn</title>
     <style>
         body {
             font-family: Arial, sans-serif;
             color: #333;
             background-color: #f9f9f9;
             padding: 20px;
         }

         .email-container {
             max-width: 600px;
             margin: 0 auto;
             background-color: #fff;
             padding: 20px;
             border: 1px solid #ddd;
             border-radius: 8px;
         }

         .button {
             display: inline-block;
             padding: 10px 20px;
             color: #fff;
             background-color: green;
             text-decoration: none;
             border-radius: 5px;
         }

         .footer {
             margin-top: 20px;
             font-size: 12px;
             color: #888;
         }
     </style>
 </head>

 <body>
     <div class="email-container">
         <h2>Xin chào [Tên người dùng],</h2>
         <p>Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản của mình. Để hoàn tất quá trình, vui lòng nhấp vào nút bên dưới:</p>
         <p style="text-align: center;">
             <a href='$resetLinkGmail' class="button">Đặt lại mật khẩu</a>
         </p>
         <p>
             Lưu ý: Liên kết này sẽ hết hạn sau <strong>24 giờ</strong> để đảm bảo an toàn. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này, tài khoản của bạn sẽ không bị ảnh hưởng.
         </p>
         <div class="footer">
             <p>Trân trọng,</p>
             <p>Đội ngũ hỗ trợ khách hàng - [Tên công ty của bạn]</p>
         </div>
         <hr>
         <p class="footer">
             Nếu bạn gặp khó khăn khi nhấp vào liên kết trên, vui lòng sao chép và dán liên kết sau vào trình duyệt của bạn:<br>
             <a href="[URL]">[URL]</a>
         </p>
     </div>
 </body>

 </html>