import aerialImg from '../../../images/waterland-sample.png'; //temporary image, replace with actual aerial view of the resort when available
import lobbyImg  from '../../../images/waterlandroom.png'; // temporary image, replace with actual lobby image of the resort when available
// if you use the logo elsewhere in this component you can import it too:
// import logo from '../../images/logo.png';

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
                        Discover the story behind paradise - where nature's beauty meets 
                        comfort, and every moment turns into a memory worth keeping.
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
                                Founded in 2005, Waterland Resort started with a simple dream: 
                                to create a place where families, couples, and friends from Kabacan 
                                and beyond could escape the everyday and enjoy the beauty of tropical life.
                            </p>

                            <p className="text-lg text-gray-600 mb-6 leading-relaxed">
                                What began as a small property by the water has grown into a 5-hectare haven, 
                                complete with cozy cottages, refreshing pools, and amenities designed for comfort and fun.
                            </p>

                            <p className="text-lg text-gray-600 leading-relaxed">
                                Today, we welcome thousands of guests each year—from intimate local celebrations 
                                to lively corporate events—all while keeping the warm, personal touch that makes 
                                Waterland Resort truly feel like home.
                            </p>
                        </div>

                        <div className="grid grid-cols-2 gap-6">
                            <img
                                src={aerialImg}
                                alt="Resort aerial view"
                                className="rounded-2xl shadow-2xl h-72 w-full object-cover hover:scale-105 transition duration-500"
                            />
                            <img
                                src={lobbyImg}
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
