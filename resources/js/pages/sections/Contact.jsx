export default function Contact() {
    return (
        <section className="py-28 text-center bg-linear-to-r from-sky-700 to-sky-500 text-white relative overflow-hidden" id="contact">
            <div className="max-w-3xl mx-auto px-6">
                <h2 className="text-5xl font-bold mb-8">
                    Experience Waterland Resort
                </h2>

                <p className="text-lg mb-10 text-sky-100">
                    Come and see why thousands of guests choose us for their perfect getaway.
                </p>

                <div className="flex justify-center gap-6">
                    <button className="bg-white text-sky-700 px-10 py-4 rounded-xl font-semibold shadow-xl hover:scale-105 transition">
                        Book Your Stay
                    </button>

                    <button className="border-2 border-white px-10 py-4 rounded-xl font-semibold hover:bg-white hover:text-sky-700 transition">
                        Contact Us
                    </button>
                </div>
            </div>
        </section>
    )
}