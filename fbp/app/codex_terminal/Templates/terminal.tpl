<div
  class="codex-terminal-root"
  data-ws-path="{$codex_terminal_ws_path|escape}"
  data-ws-token="{$codex_terminal_ws_token|escape}"
  data-token-ttl="{$codex_terminal_token_ttl|escape}"
  data-initial-input="{$codex_terminal_initial_input|escape}"
  data-font-family="Cascadia Mono, Fira Code, Menlo, Consolas, monospace"
  data-font-size="12"
  data-line-height="1.2">
  <p class="codex-terminal-status">{t key="codex_terminal.connecting"}</p>
  <div class="codex-terminal-stage" style="position:relative;">
    <div class="codex-terminal-box" style="height: 460px; width: 100%; background: #f7f9fc; border: 1px solid #d8e0ea; border-radius: 10px; padding: 6px;"></div>
    <div class="codex-terminal-focus-mask" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.42);color:#fff;border-radius:10px;font-size:12px;letter-spacing:0.03em;cursor:pointer;user-select:none;opacity:1;pointer-events:auto;transition:opacity 180ms ease;">{t key="codex_terminal.click_to_focus"}</div>
  </div>
</div>
