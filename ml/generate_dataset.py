"""
generate_dataset.py
Generates a comprehensive, realistic real-estate dataset for Indian cities and locations.
"""
import csv
import random

# Real market baseline price per sqft (INR) for each location
LOCATION_RATES = {
    # Mumbai
    ('Mumbai', 'Bandra'): 42000,
    ('Mumbai', 'Juhu'): 48000,
    ('Mumbai', 'Worli'): 45000,
    ('Mumbai', 'Lower Parel'): 38000,
    ('Mumbai', 'Dadar'): 28000,
    ('Mumbai', 'Andheri'): 24000,
    ('Mumbai', 'Powai'): 22000,
    ('Mumbai', 'Borivali'): 16000,
    ('Mumbai', 'Malad'): 15000,
    ('Mumbai', 'Thane'): 12000,

    # Delhi
    ('Delhi', 'Defence Colony'): 38000,
    ('Delhi', 'Greater Kailash'): 32000,
    ('Delhi', 'Vasant Kunj'): 26000,
    ('Delhi', 'Saket'): 22000,
    ('Delhi', 'Lajpat Nagar'): 18000,
    ('Delhi', 'Karol Bagh'): 16000,
    ('Delhi', 'Dwarka'): 13000,
    ('Delhi', 'Pitampura'): 14000,
    ('Delhi', 'Janakpuri'): 12000,
    ('Delhi', 'Rohini'): 10000,

    # Bangalore
    ('Bangalore', 'Indiranagar'): 22000,
    ('Bangalore', 'Koramangala'): 20000,
    ('Bangalore', 'HSR Layout'): 15000,
    ('Bangalore', 'Hebbal'): 14000,
    ('Bangalore', 'Whitefield'): 11000,
    ('Bangalore', 'JP Nagar'): 11500,
    ('Bangalore', 'Marathahalli'): 9500,
    ('Bangalore', 'Sarjapur'): 9000,
    ('Bangalore', 'Electronic City'): 7500,
    ('Bangalore', 'Yeshwanthpur'): 9000,

    # Hyderabad
    ('Hyderabad', 'Banjara Hills'): 24000,
    ('Hyderabad', 'Jubilee Hills'): 26000,
    ('Hyderabad', 'Hitec City'): 14000,
    ('Hyderabad', 'Gachibowli'): 13000,
    ('Hyderabad', 'Madhapur'): 13500,
    ('Hyderabad', 'Kondapur'): 11000,
    ('Hyderabad', 'Kukatpally'): 8500,
    ('Hyderabad', 'Ameerpet'): 8000,
    ('Hyderabad', 'Secunderabad'): 7500,
    ('Hyderabad', 'LB Nagar'): 6500,

    # Chennai
    ('Chennai', 'Mylapore'): 16000,
    ('Chennai', 'Adyar'): 17000,
    ('Chennai', 'T Nagar'): 15000,
    ('Chennai', 'Anna Nagar'): 14000,
    ('Chennai', 'Nungambakkam'): 16500,
    ('Chennai', 'OMR'): 9000,
    ('Chennai', 'Velachery'): 8500,
    ('Chennai', 'Sholinganallur'): 8000,
    ('Chennai', 'Porur'): 7000,
    ('Chennai', 'Ambattur'): 5500,
    ('Chennai', 'Perambur'): 5800,

    # Pune
    ('Pune', 'Koregaon Park'): 17000,
    ('Pune', 'Viman Nagar'): 13500,
    ('Pune', 'Aundh'): 13000,
    ('Pune', 'Kothrud'): 12500,
    ('Pune', 'Baner'): 11000,
    ('Pune', 'Magarpatta'): 10500,
    ('Pune', 'Wakad'): 9000,
    ('Pune', 'Hinjewadi'): 8500,
    ('Pune', 'Hadapsar'): 8000,
    ('Pune', 'Kondhwa'): 6800,
    ('Pune', 'Katraj'): 6200,

    # Kolkata
    ('Kolkata', 'Park Street'): 16000,
    ('Kolkata', 'Ballygunge'): 15000,
    ('Kolkata', 'Alipore'): 18000,
    ('Kolkata', 'Salt Lake'): 10000,
    ('Kolkata', 'New Town'): 8500,
    ('Kolkata', 'Rajarhat'): 7000,
    ('Kolkata', 'Jadavpur'): 6500,
    ('Kolkata', 'Tollygunge'): 6800,
    ('Kolkata', 'Dum Dum'): 5500,
    ('Kolkata', 'Behala'): 5200,
    ('Kolkata', 'Howrah'): 4800,

    # Ahmedabad
    ('Ahmedabad', 'Bodakdev'): 12000,
    ('Ahmedabad', 'Satellite'): 10000,
    ('Ahmedabad', 'Vastrapur'): 9500,
    ('Ahmedabad', 'Thaltej'): 9000,
    ('Ahmedabad', 'SG Highway'): 8000,
    ('Ahmedabad', 'Bopal'): 6500,
    ('Ahmedabad', 'Gota'): 5500,
    ('Ahmedabad', 'Maninagar'): 5200,
    ('Ahmedabad', 'Chandkheda'): 4800,
    ('Ahmedabad', 'Naroda'): 4200,

    # Jaipur
    ('Jaipur', 'C Scheme'): 11000,
    ('Jaipur', 'Bani Park'): 9500,
    ('Jaipur', 'Malviya Nagar'): 8500,
    ('Jaipur', 'Vaishali Nagar'): 8000,
    ('Jaipur', 'Mansarovar'): 6500,
    ('Jaipur', 'Pratap Nagar'): 5500,
    ('Jaipur', 'Jagatpura'): 5800,
    ('Jaipur', 'Tonk Road'): 5200,
    ('Jaipur', 'Ajmer Road'): 5000,
    ('Jaipur', 'Sitapura'): 4500,

    # Surat
    ('Surat', 'Vesu'): 8500,
    ('Surat', 'Piplod'): 8000,
    ('Surat', 'Althan'): 7500,
    ('Surat', 'Pal'): 6500,
    ('Surat', 'Adajan'): 6200,
    ('Surat', 'Bhatar'): 5800,
    ('Surat', 'Varachha'): 4800,
    ('Surat', 'Katargam'): 4500,
    ('Surat', 'Dindoli'): 3800,
    ('Surat', 'Udhna'): 3500,

    # Lucknow
    ('Lucknow', 'Hazratganj'): 9500,
    ('Lucknow', 'Gomti Nagar'): 8000,
    ('Lucknow', 'Mahanagar'): 7500,
    ('Lucknow', 'Aliganj'): 7000,
    ('Lucknow', 'Indira Nagar'): 6800,
    ('Lucknow', 'Vikas Nagar'): 5800,
    ('Lucknow', 'Vrindavan Yojna'): 5500,
    ('Lucknow', 'Alambagh'): 5200,
    ('Lucknow', 'Kanpur Road'): 4800,
    ('Lucknow', 'Rajajipuram'): 4500,
}

PROPERTY_TYPE_MULTIPLIERS = {
    'Apartment': 1.00,
    'Villa': 1.35,
    'Independent House': 1.18,
    'Commercial': 1.25,
    'Studio': 0.90,
}

FURNISHING_MULTIPLIERS = {
    'Unfurnished': 0.92,
    'Semi-Furnished': 1.00,
    'Furnished': 1.12,
}

CONDITION_MULTIPLIERS = {
    'Poor': 0.82,
    'Average': 0.93,
    'Good': 1.00,
    'Excellent': 1.15,
}

def generate():
    random.seed(42)
    rows = []
    
    property_types = ['Apartment', 'Villa', 'Independent House', 'Commercial', 'Studio']
    furnishing_types = ['Unfurnished', 'Semi-Furnished', 'Furnished']
    condition_types = ['Poor', 'Average', 'Good', 'Excellent']

    for (city, loc), base_rate in LOCATION_RATES.items():
        # Generate multiple samples per location across property types and sizes
        for pt in property_types:
            # 3 to 6 samples per type per location
            samples_count = 4 if pt in ['Apartment', 'Villa'] else 3
            for _ in range(samples_count):
                if pt == 'Studio':
                    area = random.randint(350, 700)
                    bedrooms = 1
                    bathrooms = 1
                    floors = 1
                elif pt == 'Apartment':
                    bedrooms = random.choice([1, 2, 2, 3, 3, 4])
                    area = int(bedrooms * random.uniform(400, 650) + random.randint(50, 200))
                    bathrooms = max(1, bedrooms - random.choice([0, 1]))
                    floors = random.randint(1, 4)
                elif pt in ['Villa', 'Independent House']:
                    bedrooms = random.choice([3, 4, 4, 5, 6])
                    area = int(bedrooms * random.uniform(500, 900) + random.randint(200, 600))
                    bathrooms = bedrooms
                    floors = random.randint(1, 3)
                else: # Commercial
                    bedrooms = random.choice([0, 1, 2])
                    area = random.choice([500, 850, 1200, 1800, 2500, 4000, 7000])
                    bathrooms = max(1, random.randint(1, 4))
                    floors = random.randint(1, 5)

                property_age = random.choice([0, 1, 2, 3, 5, 7, 10, 15, 20, 25])
                parking = random.choice([0, 1, 1, 2, 2, 3])
                furnishing = random.choice(furnishing_types)
                condition = random.choice(condition_types)

                # Compute price per sqft
                psf = base_rate * PROPERTY_TYPE_MULTIPLIERS[pt]
                psf *= FURNISHING_MULTIPLIERS[furnishing]
                psf *= CONDITION_MULTIPLIERS[condition]

                # Age depreciation
                age_factor = max(0.68, 1.0 - (property_age * 0.009))
                psf *= age_factor

                # Parking premium
                parking_factor = 1.0 + (min(parking, 3) * 0.03)
                psf *= parking_factor

                # Floor rise
                floor_factor = 1.0 + (max(0, floors - 1) * 0.02)
                psf *= floor_factor

                # Random noise +- 4%
                psf *= random.uniform(0.96, 1.04)

                price = round(area * psf, -3) # Round to nearest thousand
                price = max(500000, price)

                rows.append([
                    city, loc, pt, area, bedrooms, bathrooms,
                    floors, property_age, parking, furnishing, condition, int(price)
                ])

    return rows

if __name__ == '__main__':
    data = generate()
    header = ['city', 'location', 'property_type', 'area', 'bedrooms', 'bathrooms',
              'floors', 'property_age', 'parking', 'furnishing', 'condition', 'price']
    
    with open(r'C:\xampp\htdocs\house-price-prediction\ml\dataset.csv', 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(header)
        writer.writerows(data)

    with open(r'd:\House Price Prediction\ml\dataset.csv', 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(header)
        writer.writerows(data)

    print(f"Generated {len(data)} high-quality real estate rows successfully!")
