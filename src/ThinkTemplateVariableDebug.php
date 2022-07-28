<?php

declare(strict_types=1);

namespace app\middleware;

use Closure;
use think\App;
use think\facade\Log;
use think\facade\View;
use think\Request;
use think\Response;
use think\facade\Event;
use think\event\LogWrite;

/**
 * 模板变量调试中间件
 * - 使用此中间件将不会再记录debug类型日志
 * - 需要在log配置文件中把debug加入到level**日志记录级别**中
 */
class ThinkTemplateVariableDebug
{
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!env('app_debug') || request()->isAjax()) {
            return $response;
        }
        $template_data = View::get();
        Log::record($template_data, 'debug');
        Event::listen(LogWrite::class, function (LogWrite $log) {
            unset($log->log['debug']);
        });
        return $response;
    }
}
