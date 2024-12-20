# connect-study
授業や学校活動で使用するオプション・プラグイン  
  
Drone-Studyは以下のマニュアルを参照してください。  
https://manual.connect-cms.jp/study/dronestudies/index.html  
  
Face-Studyは以下のマニュアルを参照してください。  
https://manual.connect-cms.jp/study/facestudies/index.html  
  
Speech-Studyは以下のマニュアルを参照してください。  
https://manual.connect-cms.jp/study/speechstudies/index.html  
  
WBGTチェックは以下で説明します。  
https://github.com/opensource-workshop/connect-study/wiki/WBGT-Check

## Dronstudy

Connect-CMS v1.8.0からBlocklyが本体に同梱されなくなりました。
DronStudyを利用する場合は、当リポジトリのblockly.zipを解凍して、Connect-CMSのpublic/js/optionディレクトリに追加してください。

# オプションリポジトリ ←→ 開発環境にコピー(win)

<details>
<summary>dev_2_option_private.ps1.example</summary>

```shell
# コピー元のルートPATH
$src_root_dir = "C:\path_to_dev_connect-cms\"
# コピー先のルートPATH
$dist_root_dir = "C:\path_to_connect-study_dir\"

### コピー（robocopy <コピー元> <コピー先>）
Copy-Item -Path "${src_root_dir}composer-option.json" -Destination "${dist_root_dir}"
Copy-Item -Path "${src_root_dir}composer-option.lock" -Destination "${dist_root_dir}"
```
</details>

<details>
<summary>option_private_2_dev.ps1.example</summary>

```shell
# コピー元のルートPATH
$src_root_dir = "C:\path_to_connect-study_dir\"
# コピー先のルートPATH
$dist_root_dir = "C:\path_to_dev_connect-cms\"

Copy-Item -Path "${src_root_dir}composer-option.json" -Destination "${dist_root_dir}"
Copy-Item -Path "${src_root_dir}composer-option.lock" -Destination "${dist_root_dir}"
```
</details>
