// src/components/Navbar.jsx
// Menu navigasi dan pemilih user. Belum ada login, jadi user dipilih manual.
import { NavLink } from 'react-router-dom'
const USERS = [1, 2, 3, 4, 5]
export default function Navbar({ userId, onUserChange }) {
    return (
        <header className="navbar-wrap">
            <div className="navbar">
                <NavLink className="brand" to="/" aria-label="Toko Online, beranda">
                    <span className="brand-mark">T</span>
                    <span>Toko<span className="brand-light">Kita</span></span>
                </NavLink>
                <nav aria-label="Navigasi utama">
                    <NavLink to="/" end>
                        Produk
                    </NavLink>
                    <NavLink to="/cart">Keranjang <span className="nav-arrow">↗</span></NavLink>
                </nav>
                <label className="user">
                    <span className="user-avatar">U</span>
                    <span className="user-caption">Belanja sebagai</span>
                    <select value={userId} onChange={(e) =>
                        onUserChange(Number(e.target.value))}>
                        {USERS.map((id) => (
                            <option key={id} value={id}>
                                User {id}
                            </option>
                        ))}
                    </select>
                </label>
            </div>
        </header>
    )
}