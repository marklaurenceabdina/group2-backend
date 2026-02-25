import deluxeImg from "../../../images/waterlandroom.png"
import familyImg from "../../../images/waterland-hostel.jpg"
import cottageImg from "../../../images/cottage.jpg"

export default function Services() {

    const rooms = [
        {
            name: "Deluxe Room",
            price: "₱3,500 / night",
            image: deluxeImg,
        },
        {
            name: "Family Room",
            price: "₱5,000 / night",
            image: familyImg,
        },
        {
            name: "Private Cottage",
            price: "₱7,500 / night",
            image: cottageImg,
        },
    ]

    return (
        <section className="py-28 px-6 md:px-24 text-center bg-white" id="services">
            <h2 className="text-4xl font-bold mb-16 text-sky-800">
                Services
            </h2>

            <div className="grid md:grid-cols-3 gap-10">
                {rooms.map((room, index) => (
                    <div
                        key={index}
                        className="bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300 overflow-hidden"
                    >
                        {/* IMAGE SECTION */}
                        <div className="h-56 overflow-hidden">
                            <img
                                src={room.image}
                                alt={room.name}
                                className="w-full h-full object-cover"
                            />
                        </div>

                        <div className="p-8">
                            <h3 className="text-xl font-semibold mb-3">
                                {room.name}
                            </h3>

                            <p className="text-sky-600 font-bold mb-6">
                                {room.price}
                            </p>

                            <button className="w-full bg-sky-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-sky-700 transition">
                                View Details
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </section>
    )
}