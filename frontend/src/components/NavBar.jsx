import React from 'react'

export default function NavBar() {
  return (
    <header className="ds-navbar" aria-label="主导航">
      <div className="brand" aria-label="品牌">设计系统示例</div>
      <ul className="nav-links" role="menubar">
        <li className="nav-item" role="menuitem">首页</li>
        <li className="nav-item" role="menuitem">关于</li>
        <li className="nav-item" role="menuitem">产品</li>
        <li className="nav-item" role="menuitem">联系</li>
      </ul>
      <div className="nav-actions">
        <button className="btn btn-small btn-outline">登录</button>
      </div>
    </header>
  )
}