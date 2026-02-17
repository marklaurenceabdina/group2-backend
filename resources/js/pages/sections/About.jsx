export default function About() {
    return (
        <main className="bg-white text-gray-800" id="about">

            {/* HERO SECTION */}
            <section className="relative h-[60vh] flex items-center justify-center text-center bg-linear-to-r from-sky-800 to-sky-600">
                <div className="absolute inset-0 bg-black/40"></div>

                <div className="relative z-10 text-white px-6">
                    <h1 className="text-6xl font-bold mb-6">
                        About Waterland Resort
                    </h1>
                    <p className="text-lg md:text-xl max-w-3xl mx-auto text-sky-100">
                        Discover the story behind paradise – where luxury meets nature,
                        and every moment becomes a memory.
                    </p>
                </div>
            </section>

            {/* OUR STORY */}
            <section className="py-28 px-6 md:px-24 bg-linear-to-b from-white to-sky-50">
                <div className="max-w-6xl mx-auto">
                    <div className="grid md:grid-cols-2 gap-16 items-center">
                        <div>
                            <h2 className="text-5xl font-bold mb-8 text-sky-800">
                                Our Story
                            </h2>

                            <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                                Founded in 2015, Waterland Resort began with a simple vision:
                                to create a sanctuary where families, couples, and friends could
                                escape the ordinary and immerse themselves in tropical luxury.
                            </p>

                            <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                                What started as a small beachfront property has grown into a
                                5-hectare paradise featuring world-class amenities, private
                                cottages, and award-winning service.
                            </p>

                            <p className="text-lg text-gray-600 leading-relaxed">
                                Today, we welcome thousands of guests each year – from intimate
                                weddings to grand corporate events – all while maintaining the
                                warm, personal touch that makes Waterland truly special.
                            </p>
                        </div>

                        <div className="grid grid-cols-2 gap-6">
                            <img
                                src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b"
                                alt="Resort aerial view"
                                className="rounded-2xl shadow-2xl h-72 w-full object-cover hover:scale-105 transition duration-500"
                            />
                            <img
                                src="https://images.unsplash.com/photo-1571896349842-33c89424de2d"
                                alt="Resort lobby"
                                className="rounded-2xl shadow-2xl h-72 w-full object-cover mt-10 hover:scale-105 transition duration-500"
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* MISSION & VALUES */}
            <section className="bg-sky-50 py-28 px-6 md:px-24">
                <div className="max-w-6xl mx-auto text-center">
                    <h2 className="text-5xl font-bold mb-6 text-sky-800">
                        Our Mission & Values
                    </h2>

                    <p className="text-lg text-gray-600 mb-16 max-w-3xl mx-auto">
                        We're committed to creating unforgettable experiences while
                        preserving the natural beauty that makes our resort special.
                    </p>

                    <div className="grid md:grid-cols-3 gap-10">
                        {[
                            {
                                icon: "🌿",
                                title: "Sustainability",
                                desc: "Eco-friendly practices and responsible tourism to protect our environment."
                            },
                            {
                                icon: "🤝",
                                title: "Exceptional Service",
                                desc: "Warm, personalized hospitality that makes every guest feel like family."
                            },
                            {
                                icon: "✨",
                                title: "Creating Memories",
                                desc: "Designing experiences that guests will cherish for a lifetime."
                            }
                        ].map((item, index) => (
                            <div
                                key={index}
                                className="bg-white/80 backdrop-blur-md p-10 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300"
                            >
                                <div className="text-5xl mb-6">{item.icon}</div>
                                <h3 className="text-xl font-semibold mb-4 text-sky-600">{item.title}</h3>
                                <p className="text-gray-600">{item.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

        </main>
    );
}
