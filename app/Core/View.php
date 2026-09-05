<?php
declare(strict_types=1);

namespace XiuXian\Core;

/**
 * 简易视图渲染器
 *
 * 用法：View::render('home.index', ['name' => 'XiuXian']);
 * 视图文件：app/Views/home/index.php
 */
class View
{
    private string $viewsPath;

    public function __construct(string $viewsPath)
    {
        $this->viewsPath = rtrim($viewsPath, '/');
    }

    /**
     * 渲染视图
     */
    public function render(string $name, array $data = []): void
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $name) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("视图不存在：$name ($file)");
        }
        $this->renderFile($file, $data);
    }

    /**
     * 渲染文件（提取变量 + include）
     */
    private function renderFile(string $__file, array $__data): void
    {
        // 提取变量
        extract($__data, EXTR_SKIP);

        // 开启输出缓冲
        ob_start();
        try {
            include $__file;
            $content = ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        echo $content;
    }

    /**
     * 渲染为字符串（用于邮件等）
     */
    public function renderToString(string $name, array $data = []): string
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $name) . '.php';
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}