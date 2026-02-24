import About from "./About"
import Services from "./Services"
import Testimonials from "./Testimonials"
import Location from "./Location"
import Contact from "./Contact"
import Footer from "./Footer"

export default function Home() {
    return (
        <main className="bg-linear-to-b from-sky-50 via-white to-sky-100 text-gray-800">

            {/* HERO SECTION */}
            <section
                className="relative h-screen flex items-center justify-center text-center bg-cover bg-center"
                style={{
                    backgroundImage:
                        "url('https://images.unsplash.com/photo-1676089775678-f81f7253103e?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')",
                }}
                id="home"
            >
                <div className="absolute inset-0 bg-linear-to-br from-sky-900/70 via-black/60 to-sky-800/70"></div>

                <div className="relative z-10 text-white px-6 max-w-4xl">
                    <h1 className="text-6xl md:text-7xl font-bold mb-8 leading-tight drop-shadow-lg">
                        Escape to Paradise at Waterland Resort Kabacan
                    </h1>

                    <p className="text-lg md:text-xl mb-10 max-w-2xl mx-auto text-sky-100">
                        Relax in peaceful surroundings, swim in refreshing waters, 
                        and celebrate life's special moments in beautiful North Cotabato.
                    </p>

                    <div className="flex justify-center gap-6">
                        <button className="px-8 py-4 rounded-xl font-semibold bg-white text-sky-700 shadow-xl hover:scale-105 transition">
                            Book Now
                        </button>

                        <button className="px-8 py-4 rounded-xl font-semibold border-2 border-white hover:bg-white hover:text-sky-700 transition">
                            View Rooms
                        </button>
                    </div>
                </div>
            </section>

            <About />
            <Services />
            <Location />
            <Testimonials />
            <Contact />
            <Footer />

        </main>
    )
}
