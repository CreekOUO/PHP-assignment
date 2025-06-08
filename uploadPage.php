<?php 

session_start();
if (!isset($_SESSION['user'])) {
    echo "
    <!DOCTYPE html>
    <html lang='zh-Hant'>
    <head>
        <meta charset='UTF-8'>
        <title>請先登入</title>
        <meta http-equiv='refresh' content='3;url=login.php'>
        <style>
            body {
                font-family: sans-serif;
                background-color:#fffaf9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .message-box {
                text-align: center;
                border: 2px solid #444;
                background-color: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgb(125, 115, 137);
            }
            .message-box h2 {
                color: rgb(220, 197, 255);
                text-shadow:
                -2px 0 0 rgb(69, 28, 107),
                2px 0 0 rgb(58, 28, 107),
                0 -2px 0 rgb(47, 28, 107),
                0  2px 0 rgb(29, 28, 107);
                font-size: 32px;
                
            };
                margin-bottom: 10px;
            }
            .message-box p {
                color: #333;
            }
            .message-box a {
                display: inline-block;
                margin-top: 15px;
                text-decoration: none;
                padding: 8px 16px;
                background: #444;
                color: white;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class='message-box'>
            <h2>你未登入</h2>
            <p>上傳素材需要登入</p>
            <a href='login.php'>點我前往登入</a>
        </div>
    </body>
    </html>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>上傳素材</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f7f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #fff;
            border: 2px solid #ccc;
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }

        input[type="file"] {
            width: 70%;
        }

        .folder-icon {
            display: inline-block;
            vertical-align: middle;
            margin-left: 10px;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        button {
            padding: 8px 16px;
            border: 1px solid #333;
            background: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #eee;
        }
    </style>
</head>

<body>

    <form action="upload.php" method="post" enctype="multipart/form-data">
        <h2>Upload Your Material</h2>

        <label>Material Name</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" rows="3" required></textarea>

        <label>Cover Image</label>
        <input type="file" name="coverpath" accept="image/*" required>
        
        <label>Material File</label>
        <input type="file" name="filename" required>
        

        <label>Material Type</label>
        <select name="category" required>
            <option value="Brush">Brush</option>
            <option value="Texture">Texture</option>
            <option value="Pattern">Pattern</option>
            <option value="Manga">Manga</option>
            <option value="Template">Template</option>
            <option value="Drawing File">Drawing File</option>
        </select>

        <div class="buttons">
            <button type="button" onclick="window.location='MaterialsPage.php'">Cancel</button>
            <button type="submit">Upload</button>
        </div>
    </form>

</body>

</html>