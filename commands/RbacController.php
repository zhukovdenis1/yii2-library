<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * RBAC initialization console controller
 */
class RbacController extends Controller
{
    /**
     * Initialize RBAC roles and permissions
     *
     * @return int Exit code
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Remove old data
        $auth->removeAll();

        echo "Creating permissions...\n";

        // Create permissions
        $viewBook = $auth->createPermission('viewBook');
        $viewBook->description = 'View books';
        $auth->add($viewBook);

        $createBook = $auth->createPermission('createBook');
        $createBook->description = 'Create book';
        $auth->add($createBook);

        $updateBook = $auth->createPermission('updateBook');
        $updateBook->description = 'Update book';
        $auth->add($updateBook);

        $deleteBook = $auth->createPermission('deleteBook');
        $deleteBook->description = 'Delete book';
        $auth->add($deleteBook);

        $viewAuthor = $auth->createPermission('viewAuthor');
        $viewAuthor->description = 'View authors';
        $auth->add($viewAuthor);

        $createAuthor = $auth->createPermission('createAuthor');
        $createAuthor->description = 'Create author';
        $auth->add($createAuthor);

        $updateAuthor = $auth->createPermission('updateAuthor');
        $updateAuthor->description = 'Update author';
        $auth->add($updateAuthor);

        $deleteAuthor = $auth->createPermission('deleteAuthor');
        $deleteAuthor->description = 'Delete author';
        $auth->add($deleteAuthor);

        $subscribe = $auth->createPermission('subscribe');
        $subscribe->description = 'Subscribe to author';
        $auth->add($subscribe);

        $viewReport = $auth->createPermission('viewReport');
        $viewReport->description = 'View reports';
        $auth->add($viewReport);

        echo "Creating roles...\n";

        // Create guest role (for unauthenticated users)
        $guest = $auth->createRole('guest');
        $guest->description = 'Guest user (unauthenticated)';
        $auth->add($guest);
        $auth->addChild($guest, $viewBook);
        $auth->addChild($guest, $viewAuthor);
        $auth->addChild($guest, $subscribe);
        $auth->addChild($guest, $viewReport);

        // Create user role (authenticated user with CRUD access)
        $user = $auth->createRole('user');
        $user->description = 'Authenticated user with CRUD access';
        $auth->add($user);
        $auth->addChild($user, $viewBook);
        $auth->addChild($user, $createBook);
        $auth->addChild($user, $updateBook);
        $auth->addChild($user, $deleteBook);
        $auth->addChild($user, $viewAuthor);
        $auth->addChild($user, $createAuthor);
        $auth->addChild($user, $updateAuthor);
        $auth->addChild($user, $deleteAuthor);
        $auth->addChild($user, $subscribe);
        $auth->addChild($user, $viewReport);

        echo "RBAC initialization completed successfully.\n";

        return ExitCode::OK;
    }

    /**
     * Assign role to user
     *
     * @param int $userId User ID
     * @param string $role Role name (guest or user)
     * @return int Exit code
     */
    public function actionAssign($userId, $role = 'user')
    {
        $auth = Yii::$app->authManager;

        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            echo "Role '{$role}' not found. Please run 'php yii rbac/init' first.\n";
            return ExitCode::DATAERR;
        }

        $auth->assign($roleObject, $userId);

        echo "Role '{$role}' has been assigned to user {$userId}.\n";

        return ExitCode::OK;
    }

    public function actionClear()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        echo "All RBAC data has been cleared.\n";
        return ExitCode::OK;
    }
}