<?php
namespace src;

class UserCookieManager {
    private $cookieName = 'user_data';
    private $cookieExpiry = 86400; // 24 hours in seconds
    private $cookiePath = '/';
    private $isSecure = false; // Set to true if using HTTPS
    private $isHttpOnly = true;

    public function setUserCookie(array $userData): bool {
        // Encode user data as JSON
        $encodedData = json_encode($userData);
        
        // Set cookie with encoded data
        return setcookie(
            $this->cookieName,
            $encodedData,
            time() + $this->cookieExpiry,
            $this->cookiePath,
            '',  // domain
            $this->isSecure,
            $this->isHttpOnly
        );
    }

    public function getUserCookie(): ?array {
        if (!isset($_COOKIE[$this->cookieName])) {
            return null;
        }

        // Decode JSON data from cookie
        $userData = json_decode($_COOKIE[$this->cookieName], true);
        
        return is_array($userData) ? $userData : null;
    }

    public function clearUserCookie(): bool {
        // Delete cookie by setting expiration to past time
        return setcookie(
            $this->cookieName,
            '',
            time() - 3600,
            $this->cookiePath,
            '',
            $this->isSecure,
            $this->isHttpOnly
        );
    }

    public function updateUserCookie(array $newData): bool {
        $currentData = $this->getUserCookie();
        if ($currentData === null) {
            return $this->setUserCookie($newData);
        }

        // Merge existing data with new data
        $updatedData = array_merge($currentData, $newData);
        return $this->setUserCookie($updatedData);
    }

    // Getter and setter for cookie configuration
    public function setCookieExpiry(int $seconds): void {
        $this->cookieExpiry = $seconds;
    }

    public function setCookiePath(string $path): void {
        $this->cookiePath = $path;
    }

    public function setSecure(bool $secure): void {
        $this->isSecure = $secure;
    }

    public function setHttpOnly(bool $httpOnly): void {
        $this->isHttpOnly = $httpOnly;
    }
}