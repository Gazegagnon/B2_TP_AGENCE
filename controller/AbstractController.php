<?php
abstract class AbstractController {

    protected string $viewsPath;

    public function __construct() {
        $this->viewsPath = realpath(__DIR__ . '/../views');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function getToken(): string {
        return $_SESSION['csrf_token'];
    }

    public function isValidCsrf(?string $tokenForm): bool {
        return !empty($tokenForm) && hash_equals($_SESSION['csrf_token'], $tokenForm);
    }

    protected function flash(string $type, string $message): void {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Coordonnées affichées côté client (messagerie, footer, compte).
     *
     * @return array{email: string, phone_display: string, phone_href: string}
     */
    protected function clientSupportInfo(): array
    {
        return [
            'email' => 'service.client@locautopro.fr',
            'phone_display' => '09 70 35 12 34',
            'phone_href' => '+33970351234',
        ];
    }

    /** @return array<int, array{type: string, message: string}> */
    private function pullFlashes(): array {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return is_array($f) ? $f : [];
    }

    public function render(string $view, array $data = [], ?string $pageTitle = null): void {
        $resolvedTitle = $pageTitle ?? ($data['pageTitle'] ?? null);
        unset($data['pageTitle']);

        if ($this->viewsPath === false) {
            throw new Exception("Répertoire des vues introuvable");
        }

        $page = $this->viewsPath . DIRECTORY_SEPARATOR . str_replace(['..','\\'], '', $view) . '.php';
        $real = realpath($page);

        $root = $this->viewsPath;
        if ($real === false) {
            throw new Exception("Cette page n'existe pas ou accès refusé");
        }
        $realNorm = strtolower(str_replace('\\', '/', $real));
        $rootNorm = strtolower(str_replace('\\', '/', $root));
        if (!str_starts_with($realNorm, $rootNorm)) {
            throw new Exception("Cette page n'existe pas ou accès refusé");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $real;
        $content = ob_get_clean();

        $template = $this->viewsPath . DIRECTORY_SEPARATOR . 'template.php';
        if (!file_exists($template)) {
            throw new Exception("Le fichier template.php est introuvable : $template");
        }

        $pageTitle = $resolvedTitle ?? 'LocAuto Pro — Location de véhicules';
        $flashMessages = $this->pullFlashes();

        include $template;
    }

    public function isConnected(): bool {
        return !empty($_SESSION['user_id']);
    }

    public function isCommercial(): bool {
        return $this->isConnected() && ($_SESSION['role'] ?? '') === 'COMMERCIAL';
    }

    public function isAdmin(): bool {
        return $this->isConnected() && ($_SESSION['role'] ?? '') === 'ADMIN';
    }

    public function getUser(): ?User {
        if (!$this->isConnected()) return null;
        $userId = intval($_SESSION['user_id']);
        $userModel = new UserModel();
        return $userModel->getUserById($userId);
    }

    public function redirect(string $path): void {
        header("Location: " . $path);
        exit;
    }

    protected function sanitizeString(?string $s): ?string {
        return $s === null ? null : trim(htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Compteurs pour menu admin (notifications / messagerie).
     *
     * @return array{notif: int, msg: int}
     */
    protected function adminSidebarCounts(): array
    {
        $uid = (int) ($_SESSION['user_id'] ?? 0);
        $n = 0;
        $m = 0;
        try {
            $n = (new AdminNotificationModel())->countUnread();
        } catch (Throwable $e) {
        }
        try {
            if ($uid > 0) {
                $m = (new MessageModel())->countUnreadFor($uid);
            }
        } catch (Throwable $e) {
        }

        return ['notif' => $n, 'msg' => $m];
    }
}
