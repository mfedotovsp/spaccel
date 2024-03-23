<?php


namespace app\models\forms;

use app\models\User;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;

/**
 * Форма для загрузки и обновления аватарки
 *
 * Class AvatarForm
 * @package app\models\forms
 *
 * @property int $userId                Идентификатор записи в таб. user
 * @property mixed $loadImage           Загружаемый файл
 * @property string $imageMax           Название файла
 */
class AvatarForm extends Model
{
    public $userId;
    public $loadImage;
    public $imageMax;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['userId'], 'integer'],
            [['imageMax'], 'string', 'max' => 255],
            [['loadImage'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpeg'],
        ];
    }


    /**
     * AvatarForm constructor.
     * @param int $userId
     * @param array $config
     */
    public function __construct($userId, array $config = [])
    {
        $user = User::findOne($userId);
        $this->setUserId($user->getId());
        parent::__construct($config);
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function loadMinImage(): bool
    {
        $user = User::findOne($this->getUserId());

        if ($_POST['imageMin']) {

            $path = UPLOAD . 'user-' . $this->getUserId() . '/avatar/';
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }

            $str = Yii::$app->security->generateRandomString(8);
            $file = 'avatar_' . $str . '_min.png';
            $uploadfile = $path . $file;

            $img = str_replace('data:image/png;base64,', '', $_POST['imageMin']);
            $img = str_replace(' ', '+', $img);
            $fileData = base64_decode($img);

            $url = $uploadfile;
            file_put_contents($url, $fileData);

            // Обновление аватарки
            if ($_POST['imageMax']) {

                if ($this->deleteOldAvatarImages()) {

                    $user->setAvatarMaxImage($_POST['imageMax']);
                    $user->setAvatarImage($file);
                    $user->update();

                    return true;
                }
            } else {
                // Редактирование аватарки
                unlink($path . $user->getAvatarImage());
                $user->setAvatarImage($file);
                return (bool)$user->update();
            }
        }
        return false;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function loadMaxImage(): array
    {
        $path = UPLOAD.'user-'.$this->getUserId().'/avatar/';
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        $uploadfile = $path . $_FILES['file']['name'];
        $arr = array();

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            $arr['success'] = true;
            $arr['path_max'] = $uploadfile;
            $arr['imageMax'] = $_FILES['file']['name'];
        } else {
            $arr['error'] = true;
        }

        return $arr;
    }

    /**
     * @return bool
     */
    public function deleteOldAvatarImages (): bool
    {
        $user = User::findOne($this->getUserId());
        $path = UPLOAD . 'user-' . $user->getId() . '/avatar/';

        if (is_file($path . $user->getAvatarMaxImage())) {
            unlink($path . $user->getAvatarMaxImage());
        }
        if (is_file($path . $user->getAvatarImage())) {
            unlink($path . $user->getAvatarImage());
        }

        $user->setAvatarMaxImage();
        $user->setAvatarImage();
        $user->save();

        return true;
    }


    /**
     * @return bool
     */
    public function deleteUnusedImage (): bool
    {
        if ($_POST['imageMax']) {

            $user = User::findOne($this->getUserId());
            $path = UPLOAD . 'user-' . $user->getId() . '/avatar/';
            unlink($path . $_POST['imageMax']);
            return true;
        }
        return false;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getLoadImage()
    {
        return $this->loadImage;
    }

    /**
     * @param mixed $loadImage
     */
    public function setLoadImage($loadImage): void
    {
        $this->loadImage = $loadImage;
    }

    /**
     * @return string
     */
    public function getImageMax(): string
    {
        return $this->imageMax;
    }

    /**
     * @param string $imageMax
     */
    public function setImageMax(string $imageMax): void
    {
        $this->imageMax = $imageMax;
    }

}
