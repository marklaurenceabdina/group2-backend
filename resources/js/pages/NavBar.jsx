import { useState } from "react";
import { Link } from "react-router-dom";
import photo from "../../images/temp-logo.png";

export default function NavBar() {
    const [menuOpen, setMenuOpen] = useState(false);

    return (
        <nav className="fixed w-full top-0 left-0 z-50 backdrop-blur-md bg-white/80 shadow-md border-b border-surface">
            <div className="max-w-7xl mx-auto px-6 md:px-0 py-4 flex items-center justify-between relative">

                {/* LEFT - LOGO */}
                <a
                    href="#home"
                    className="flex items-center gap-3 text-2xl font-bold text-primary-text tracking-wide"
                >
                    <img src={photo} alt="logo" className="w-12 h-12 rounded-full shadow-md" />
                    <h2 className="hidden sm:block">Waterland Resort</h2>
                </a>

                {/* DESKTOP LINKS */}
                <div className="hidden md:flex gap-8 items-center absolute left-1/2 transform -translate-x-1/2 text-primary-text font-medium">
                    {["home", "about", "services", "location", "testimonials", "contact", "footer"].map((item) => (
                        <a
                            key={item}
                            href={`#${item}`}
                            className="relative group transition"
                        >
                            <span className="capitalize hover:text-secondary transition">
                                {item}
                            </span>
                            <span className="absolute left-0 -bottom-1 w-0 h-0.5 bg-secondary transition-all group-hover:w-full"></span>
                        </a>
                    ))}
                </div>

                {/* RIGHT - AUTH */}
                <div className="hidden md:flex gap-6 items-center">
                    <Link
                        to="/login"
                        className="font-medium text-primary-text hover:text-secondary transition"
                    >
                        Log In
                    </Link>

                    <Link
                        to="/signup"
                        className="bg-primary text-white px-6 py-2.5 rounded-xl shadow-md hover:bg-secondary transition"
                    >
                        Sign Up
                    </Link>
                </div>

                {/* MOBILE BUTTON */}
                <button
                    className="md:hidden text-3xl text-primary-text"
                    onClick={() => setMenuOpen(!menuOpen)}
                >
                    ☰
                </button>
            </div>

            {/* MOBILE DROPDOWN */}
            {menuOpen && (
                <div className="md:hidden backdrop-blur-lg bg-surface shadow-xl py-8 px-6 flex flex-col gap-6 text-primary-text">

                    <div className="flex flex-col items-center gap-6 font-medium">
                        {["home", "about", "services", "location", "testimonials", "contact", "footer"].map((item) => (
                            <a
                                key={item}
                                href={`#${item}`}
                                className="hover:text-secondary transition text-lg"
                                onClick={() => setMenuOpen(false)}
                            >
                                {item.charAt(0).toUpperCase() + item.slice(1)}
                            </a>
                        ))}
                    </div>

                    <div className="border-t border-primary/20 my-4"></div>

                    <div className="flex flex-col gap-4">
                        <Link
                            to="/login"
                            className="text-center py-3 rounded-xl border border-primary font-medium hover:text-secondary transition"
                            onClick={() => setMenuOpen(false)}
                        >
                            Log In
                        </Link>

                        <Link
                            to="/signup"
                            className="text-center py-3 rounded-xl bg-primary text-white font-semibold hover:bg-secondary transition"
                            onClick={() => setMenuOpen(false)}
                        >
                            Sign Up
                        </Link>
                    </div>
                </div>
            )}
        </nav>
    );
}
