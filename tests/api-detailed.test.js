// tests/api-detailed.test.js

const request = require('supertest');
const baseURL = 'http://127.0.0.1:8000';

let createdPlanId;

beforeAll(async () => {
    // Leere den Cache vor den Tests
    await request(baseURL).post('/api/cache/clear');
});

beforeEach(async () => {
    // Leere den Cache vor jedem Test
    await request(baseURL).post('/api/cache/clear');

    // Erstellen des initialen Plans
    const initialPlan = {
        name: 'Initial Plan',
        phone: '123-456-7890',
        email: 'initial@example.com',
        details: 'Initial Details',
        eligible_for_emergency: true
    };
    await request(baseURL).post('/api/emergency-plan').send(initialPlan);

    // Erstellen von "John Doe" zur Erfüllung des GET-Tests
    const johnDoe = {
        name: 'John Doe',
        phone: '123-456-7890',
        email: 'johndoe@example.com',
        details: 'John Doe Details',
        eligible_for_emergency: true
    };
    const response = await request(baseURL).post('/api/emergency-plan').send(johnDoe);
    createdPlanId = response.body.id;
});

afterEach(async () => {
    // Leere den Cache nach jedem Test
    await request(baseURL).post('/api/cache/clear');
});

describe('API Detailed Tests', () => {
    it('GET /api/emergency-service should return emergency service data', async () => {
        const response = await request(baseURL).get('/api/emergency-service');
        expect(response.status).toBe(200);
        expect(response.body).toHaveProperty('data');
        expect(response.body.data).toEqual(expect.arrayContaining([
            expect.objectContaining({
                id: expect.any(Number),
                name: 'John Doe',
                phone: '123-456-7890',
                email: 'johndoe@example.com',
                details: 'John Doe Details',
                eligible_for_emergency: true
            })
        ]));
    });

    it('POST /api/emergency-plan should create a new emergency plan', async () => {
        const newPlan = {
            name: 'Test Plan',
            phone: '123-456-7890',
            email: 'test@example.com',
            details: 'Test Details',
            eligible_for_emergency: true
        };
        const response = await request(baseURL).post('/api/emergency-plan').send(newPlan);
        if (response.status !== 201) {
            console.log('POST Fehler:', response.body);
        }
        expect(response.status).toBe(201);
        expect(response.body).toHaveProperty('id');
        expect(response.body).toHaveProperty('status', 'success');
    });

    it('PUT /api/emergency-plan/:id should update an existing emergency plan', async () => {
        const updatedPlan = {
            name: 'Updated Plan',
            phone: '123-456-7890',
            email: 'updated@example.com',
            details: 'Updated Details',
            eligible_for_emergency: true
        };
        const response = await request(baseURL).put(`/api/emergency-plan/${createdPlanId}`).send(updatedPlan);
        if (response.status !== 200) {
            console.log('PUT Fehler:', response.body);
        }
        expect(response.status).toBe(200);
        expect(response.body).toHaveProperty('status', 'success');
    });
});
